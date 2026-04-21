# Frontend/Backend Integration Design

**Date:** 2026-04-21

**Status:** Approved for planning

## Goal

Integrate the existing Next.js frontend and Laravel backend so the application supports:

- guest access to the publication feed
- user registration
- user login and logout using the existing backend token flow
- session restoration after page refresh
- authenticated create, update, and delete operations for publications

## Scope

### In Scope

- backend route changes required for a public feed
- frontend API client for Laravel requests
- frontend auth state and browser persistence
- registration page
- login page
- public feed page backed by real API data
- create publication flow
- edit publication flow
- delete publication flow
- auth-aware navigation and action visibility
- backend and frontend verification for the integrated flow

### Out of Scope

- comments
- reactions
- reports
- moderation workflows
- admin dashboard workflows
- media upload implementation
- category management UX beyond using existing backend fields if needed

## Current State

### Backend

- Laravel 13 API with Sanctum token support
- `POST /auth/register` returns `token` and `user`
- `POST /auth/login` returns `token` and `user`
- `POST /auth/logout` and `GET /auth/me` require authentication
- publication routes currently exist, but `GET /publications` and `GET /publications/{publication}` are inside the authenticated group

### Frontend

- Next.js 16 App Router application
- pages are mostly static mockups
- no shared API layer
- no auth provider or persisted session state
- no real feed rendering from backend data
- no real CRUD implementation for publications

## Chosen Approach

Use direct frontend-to-backend API calls with the existing backend bearer token flow.

### Why This Approach

- it matches the current backend contract instead of redesigning authentication
- it is the smallest integration path for the requested scope
- it avoids introducing a proxy layer before the application has a stable client API
- it keeps Laravel as the source of truth for auth and publication permissions

## Architecture

### Backend Responsibilities

- own authentication and token issuance
- expose publication read and write endpoints
- enforce authorization for create, update, and delete operations
- continue returning validation and authorization errors in Laravel JSON responses

### Frontend Responsibilities

- render public and authenticated UI
- store the backend token in browser persistence
- restore session state on app load
- call Laravel endpoints through a shared API helper
- show and hide create, edit, and delete controls based on auth state and ownership

## Backend Design

### Route Visibility

Move the following routes outside the authenticated `auth:sanctum` group:

- `GET /publications`
- `GET /publications/{publication}`

Keep the following routes protected:

- `POST /publications`
- `PATCH /publications/{publication}`
- `DELETE /publications/{publication}`
- `POST /auth/logout`
- `GET /auth/me`

### Auth Contract

Keep the existing backend auth contract unchanged:

- registration returns a bearer token and user payload
- login returns a bearer token and user payload
- authenticated frontend requests send `Authorization: Bearer <token>`

### Publication Contract

The frontend will consume the existing publication payloads from:

- `GET /publications`
- `GET /publications/{publication}`
- `POST /publications`
- `PATCH /publications/{publication}`
- `DELETE /publications/{publication}`

Required fields for create:

- `title`
- `text`

Optional fields:

- `category_id`
- `media_type`

## Frontend Design

### Environment Contract

Add a frontend environment variable for the backend base URL.

Expected usage:

- local frontend reads `NEXT_PUBLIC_API_BASE_URL`
- requests are built relative to that base URL

The backend local environment must also allow the frontend origin through CORS and any required local app URL settings.

### Shared API Layer

Create a small API module that:

- builds request URLs from the configured backend base URL
- sends JSON requests with the correct headers
- attaches the bearer token when available
- parses Laravel JSON responses
- normalizes failed requests into a consistent frontend error shape

This layer should expose methods for:

- `register`
- `login`
- `logout`
- `getCurrentUser`
- `listPublications`
- `getPublication`
- `createPublication`
- `updatePublication`
- `deletePublication`

### Auth State

Create a frontend auth provider that owns:

- current user
- current token
- loading state during session restore
- login action
- register action
- logout action

Persist the token and user in browser storage so the session survives refresh.

On initial app load:

1. read the stored token
2. if no token exists, stay logged out
3. if a token exists, call `GET /auth/me`
4. if the request succeeds, restore the user session
5. if the request fails with `401`, clear stored auth data

### Navigation Behavior

When logged out:

- show login and registration entry points
- keep feed visible
- keep create, edit, and delete actions unavailable

When logged in:

- show logout
- show create-post entry point
- show edit and delete actions only on the user's own publications

### Page Behavior

#### Registration Page

- collect email, password, and password confirmation
- submit to `POST /auth/register`
- on success, store token and user
- redirect to the feed

#### Login Page

- collect email and password
- submit to `POST /auth/login`
- on success, store token and user
- redirect to the feed

#### Feed Page

- available to guests and authenticated users
- load publications from `GET /publications`
- show loading, empty, and error states
- show ownership actions only when the logged-in user owns a post

#### Create Publication Page

- available only to authenticated users
- collect `title` and `text`
- optionally carry `category_id` and `media_type` if exposed by the UI
- submit to `POST /publications`
- on success, navigate back to the feed or the created post view

#### Edit Publication Page

- available only to authenticated users
- load current values from `GET /publications/{publication}`
- allow editing only when the logged-in user owns the publication
- submit changes to `PATCH /publications/{publication}`
- on success, navigate to the updated publication or feed

#### Delete Publication

- available only to authenticated users
- shown only when the logged-in user owns the publication
- requires explicit confirmation before calling `DELETE /publications/{publication}`
- on success, remove the post from the current view or refetch the feed

## Data Flow

### Guest Feed

1. user opens the feed page
2. frontend calls `GET /publications` without a token
3. backend returns paginated visible publications
4. frontend renders the feed

### Registration

1. user submits registration form
2. frontend calls `POST /auth/register`
3. backend validates and creates the user and profile
4. backend returns `token` and `user`
5. frontend stores both and redirects to the feed

### Login

1. user submits login form
2. frontend calls `POST /auth/login`
3. backend validates credentials
4. backend returns `token` and `user`
5. frontend stores both and redirects to the feed

### Session Restore

1. frontend reads stored auth data on app startup
2. frontend calls `GET /auth/me` with the stored token
3. backend returns the authenticated user if the token is valid
4. frontend restores the session or clears stale auth data

### Create Publication

1. authenticated user opens the create page
2. frontend submits `title` and `text` to `POST /publications`
3. backend creates the content and publication records
4. frontend updates the feed view or redirects after success

### Update Publication

1. authenticated owner opens the edit page
2. frontend loads existing publication data
3. frontend submits changes to `PATCH /publications/{publication}`
4. backend validates ownership and saves the update
5. frontend refreshes the visible publication data

### Delete Publication

1. authenticated owner confirms deletion
2. frontend calls `DELETE /publications/{publication}`
3. backend soft-deletes by updating content status
4. frontend removes the publication from the visible list

## Error Handling

### Form Errors

- show Laravel validation messages inline on registration, login, create, and edit forms
- keep submitted values in the form when validation fails

### Auth Failures

- if an authenticated request returns `401`, clear local auth state
- redirect the user to login for auth-only pages and actions

### Feed Failures

- show an explicit loading state during fetch
- show an explicit empty state when no publications exist
- show an explicit error state when the request fails

### Mutation Failures

- show a visible success or failure message after create, update, and delete
- do not silently fail or silently refresh with no feedback

## Testing Strategy

### Backend Tests

Add or update feature tests to cover:

- guest can read the publication list
- guest can read a single publication
- guest cannot create a publication
- guest cannot update a publication
- guest cannot delete a publication
- authenticated user can create a publication
- author can update their own publication
- author can delete their own publication
- non-author cannot update another user's publication
- non-author cannot delete another user's publication

### Frontend Tests

Add tests for:

- auth storage restores session correctly
- login stores token and user
- registration stores token and user
- feed loads public publications
- create, update, and delete requests attach the bearer token
- `401` responses clear session state

### Minimum Verification

If the frontend test harness is not fully set up yet, the minimum completion bar is:

- backend automated tests for route access and publication authorization
- frontend lint passes
- manual smoke test for register, login, logout, read feed, create, edit, delete, and refresh session restore

## Missing Parts This Work Will Fill

- public publication feed access in backend routes
- frontend backend-URL configuration
- shared API client
- shared auth provider
- registration page
- real login form behavior
- real feed data rendering
- real create publication flow
- real edit publication flow
- real delete publication flow
- auth-aware navigation and ownership-aware actions

## Risks and Constraints

- storing a bearer token in browser storage is acceptable for this first pass, but it is weaker than an HTTP-only cookie model
- frontend ownership checks are convenience only; backend authorization remains the real enforcement layer
- pagination exists on the backend response and the frontend should not assume a flat unpaginated array
- existing mockup pages may need moderate restructuring to support real form and loading states cleanly

## Acceptance Criteria

- guests can open the feed and read publications
- new users can register from the frontend and become logged in immediately
- existing users can log in from the frontend using the backend token flow
- refreshing the frontend restores a valid logged-in session
- authenticated users can create publications
- authenticated users can edit their own publications
- authenticated users can delete their own publications
- users cannot edit or delete publications they do not own
- logging out clears frontend session state and ends the current backend token session
- the integrated flow is covered by backend tests and frontend verification
