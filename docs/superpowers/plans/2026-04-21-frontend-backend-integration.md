# Frontend/Backend Integration Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Integrate the Next.js frontend and Laravel backend for registration, login, logout, public publication feed, session restore, and authenticated publication create/update/delete flows.

**Architecture:** Keep Laravel as the source of truth for auth and publication permissions, and let the Next.js app talk to it directly through a shared `fetch` client using the existing bearer-token responses from `/auth/register` and `/auth/login`. Use a client-side auth provider with browser persistence, render the feed publicly, and gate create/edit/delete actions by auth state and publication ownership.

**Tech Stack:** Laravel 13, Sanctum bearer tokens, PHPUnit, Next.js 16 App Router, React 19, TypeScript, Tailwind CSS 4, Vitest, React Testing Library, jsdom

---

## Working Directories

- Run backend commands from `backend/`
- Run frontend commands from `frontend/`

## File Structure

### Backend

- Modify: `backend/routes/api.php`
  - Move publication `GET` routes into the public section while leaving mutations protected.
- Create: `backend/tests/Feature/PublicationApiTest.php`
  - Cover guest feed access, guest mutation denial, and author-vs-non-author publication mutation behavior.

### Frontend

- Modify: `frontend/package.json`
  - Add frontend test scripts and test dependencies.
- Create: `frontend/vitest.config.ts`
  - Configure Vitest with jsdom and the `@/` alias.
- Create: `frontend/src/test/setup.ts`
  - Register `jest-dom`, cleanup hooks, and browser-state resets.
- Create: `frontend/.env.example`
  - Document `NEXT_PUBLIC_API_BASE_URL`.
- Create: `frontend/src/lib/contracts.ts`
  - Shared API types for user, profile, auth responses, publications, and pagination.
- Create: `frontend/src/lib/auth-storage.ts`
  - Read, write, and clear persisted auth state.
- Create: `frontend/src/lib/api.ts`
  - Shared request helper and typed API methods for auth and publications.
- Create: `frontend/src/lib/__tests__/auth-storage.test.ts`
  - Unit tests for session persistence helpers.
- Create: `frontend/src/lib/__tests__/api.test.ts`
  - Unit tests for request behavior and bearer-token wiring.
- Create: `frontend/src/components/auth/auth-provider.tsx`
  - Client auth context, session restore, and auth actions.
- Create: `frontend/src/components/auth/use-auth.ts`
  - Small hook wrapper around the auth context.
- Create: `frontend/src/components/auth/auth-shell.tsx`
  - Shared visual wrapper for login and registration pages.
- Create: `frontend/src/components/auth/login-form.tsx`
  - Real login form using backend email/password auth.
- Create: `frontend/src/components/auth/register-form.tsx`
  - Real registration form with password confirmation.
- Create: `frontend/src/components/auth/__tests__/auth-provider.test.tsx`
  - Tests for session restore and logout cleanup.
- Create: `frontend/src/components/auth/__tests__/auth-forms.test.tsx`
  - Tests for login and registration form submission behavior.
- Modify: `frontend/src/app/layout.tsx`
  - Wrap the app in the auth provider.
- Modify: `frontend/src/components/Navigation/TopNavBar.tsx`
  - Show login/register for guests and create/logout for authenticated users.
- Modify: `frontend/src/components/Navigation/SideNavBar.tsx`
  - Remove broken out-of-scope links and keep navigation aligned to implemented routes.
- Delete: `frontend/src/app/page.tsx`
  - Remove the duplicate root page so `/` resolves only through the `(main)` route group.
- Modify: `frontend/src/app/(auth)/login/page.tsx`
  - Replace static mockup form wiring with the real login form.
- Create: `frontend/src/app/(auth)/register/page.tsx`
  - Add the registration route.
- Create: `frontend/src/components/publications/publication-card.tsx`
  - Shared post card rendering with ownership actions.
- Create: `frontend/src/components/publications/publication-feed.tsx`
  - Public feed loading, error, and empty states.
- Create: `frontend/src/components/publications/publication-editor.tsx`
  - Shared create/edit publication form component.
- Create: `frontend/src/components/publications/require-auth.tsx`
  - Client-side redirect guard for authenticated-only pages.
- Create: `frontend/src/components/publications/__tests__/publication-feed.test.tsx`
  - Feed rendering tests for loading, empty, and authenticated ownership states.
- Create: `frontend/src/components/publications/__tests__/publication-editor.test.tsx`
  - Tests for create and edit flows against the shared editor.
- Modify: `frontend/src/app/(main)/page.tsx`
  - Replace hard-coded cards with the real publication feed.
- Modify: `frontend/src/app/(main)/create/page.tsx`
  - Use the shared editor in create mode behind the auth guard.
- Create: `frontend/src/app/(main)/publications/[publicationId]/edit/page.tsx`
  - Use the shared editor in edit mode.
- Modify: `frontend/src/app/(main)/dashboard/page.tsx`
  - Fix current lint-blocking unescaped quote strings.
- Modify: `frontend/src/app/(main)/moderation/page.tsx`
  - Fix current lint-blocking unescaped quote strings.

## Task 1: Make Publication Reads Public and Lock the Backend Contract with Tests

**Files:**
- Create: `backend/tests/Feature/PublicationApiTest.php`
- Modify: `backend/routes/api.php`

- [ ] **Step 1: Write the failing backend API contract tests**

```php
<?php

namespace Tests\Feature;

use App\Models\Content;
use App\Models\Publication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PublicationApiTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(string $email = 'author@example.com'): User
    {
        $user = User::query()->create([
            'email' => $email,
            'password' => 'password123',
            'status' => 'active',
        ]);

        $user->profile()->create([
            'display_name' => str($email)->before('@')->value(),
        ]);

        return $user;
    }

    private function createPublication(User $author, array $overrides = []): Publication
    {
        $content = Content::query()->create([
            'type' => 'publication',
            'status' => $overrides['status'] ?? 'visible',
            'author_id' => $author->id,
        ]);

        return Publication::query()->create([
            'id' => $content->id,
            'title' => $overrides['title'] ?? 'Test publication',
            'text' => $overrides['text'] ?? 'Body copy',
            'media_type' => $overrides['media_type'] ?? null,
            'category_id' => $overrides['category_id'] ?? null,
        ]);
    }

    public function test_guest_can_list_visible_publications(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->getJson('/api/publications');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $publication->id)
            ->assertJsonPath('data.0.title', 'Test publication');
    }

    public function test_guest_can_view_single_visible_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author, [
            'title' => 'Visible post',
        ]);

        $response = $this->getJson("/api/publications/{$publication->id}");

        $response->assertOk()
            ->assertJsonPath('id', $publication->id)
            ->assertJsonPath('title', 'Visible post');
    }

    public function test_guest_cannot_create_a_publication(): void
    {
        $response = $this->postJson('/api/publications', [
            'title' => 'Guest title',
            'text' => 'Guest body',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_update_a_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Updated by guest',
        ]);

        $response->assertUnauthorized();
    }

    public function test_guest_cannot_delete_a_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertUnauthorized();
    }

    public function test_authenticated_user_can_create_a_publication(): void
    {
        $author = $this->createUser();
        Sanctum::actingAs($author);

        $response = $this->postJson('/api/publications', [
            'title' => 'Created title',
            'text' => 'Created body',
        ]);

        $response->assertCreated()
            ->assertJsonPath('title', 'Created title')
            ->assertJsonPath('author_id', $author->id);
    }

    public function test_author_can_update_their_own_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);
        Sanctum::actingAs($author);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Revised title',
        ]);

        $response->assertOk()
            ->assertJsonPath('title', 'Revised title');
    }

    public function test_non_author_cannot_update_someone_elses_publication(): void
    {
        $author = $this->createUser();
        $intruder = $this->createUser('intruder@example.com');
        $publication = $this->createPublication($author);
        Sanctum::actingAs($intruder);

        $response = $this->patchJson("/api/publications/{$publication->id}", [
            'title' => 'Intruder title',
        ]);

        $response->assertForbidden();
    }

    public function test_author_can_delete_their_own_publication(): void
    {
        $author = $this->createUser();
        $publication = $this->createPublication($author);
        Sanctum::actingAs($author);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertOk()
            ->assertJsonPath('message', 'Publication deleted.');

        $this->assertDatabaseHas('contents', [
            'id' => $publication->id,
            'status' => 'deleted',
        ]);
    }

    public function test_non_author_cannot_delete_someone_elses_publication(): void
    {
        $author = $this->createUser();
        $intruder = $this->createUser('intruder@example.com');
        $publication = $this->createPublication($author);
        Sanctum::actingAs($intruder);

        $response = $this->deleteJson("/api/publications/{$publication->id}");

        $response->assertForbidden();
    }
}
```

- [ ] **Step 2: Run the backend test suite and verify the guest-read tests fail**

Run:

```bash
php artisan test --filter=PublicationApiTest
```

Expected:

```text
FAIL  Tests\Feature\PublicationApiTest
X guest can list visible publications
X guest can view single visible publication
Expected response status code [200] but received 401.
```

- [ ] **Step 3: Move the publication `GET` routes into the public route section**

Update `backend/routes/api.php` so the top of the file looks like this:

```php
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/publications', [PublicationController::class, 'index']);
Route::get('/publications/{publication}', [PublicationController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/auth/me', [AuthController::class, 'me']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::post('/publications', [PublicationController::class, 'store']);
    Route::patch('/publications/{publication}', [PublicationController::class, 'update']);
    Route::delete('/publications/{publication}', [PublicationController::class, 'destroy']);
```

- [ ] **Step 4: Re-run the backend API suite and verify the full publication contract passes**

Run:

```bash
php artisan test --filter=PublicationApiTest
```

Expected:

```text
PASS  Tests\Feature\PublicationApiTest
Tests: 10 passed
```

- [ ] **Step 5: Commit the backend route contract change**

```bash
git add tests/Feature/PublicationApiTest.php routes/api.php
git commit -m "test: lock public publication access contract"
```

## Task 2: Add Frontend Test Tooling and Session Persistence Helpers

**Files:**
- Modify: `frontend/package.json`
- Create: `frontend/vitest.config.ts`
- Create: `frontend/src/test/setup.ts`
- Create: `frontend/src/lib/contracts.ts`
- Create: `frontend/src/lib/auth-storage.ts`
- Create: `frontend/src/lib/__tests__/auth-storage.test.ts`

- [ ] **Step 1: Add frontend test scripts, test dependencies, and Vitest config**

Update `frontend/package.json`:

```json
{
  "scripts": {
    "dev": "next dev",
    "build": "next build",
    "start": "next start",
    "lint": "eslint",
    "test": "vitest run",
    "test:watch": "vitest"
  },
  "devDependencies": {
    "@tailwindcss/postcss": "^4",
    "@testing-library/jest-dom": "^6.6.3",
    "@testing-library/react": "^16.0.1",
    "@testing-library/user-event": "^14.5.2",
    "@types/node": "^20",
    "@types/react": "^19",
    "@types/react-dom": "^19",
    "eslint": "^9",
    "eslint-config-next": "16.2.3",
    "jsdom": "^25.0.1",
    "tailwindcss": "^4",
    "typescript": "^5",
    "vitest": "^2.1.4"
  }
}
```

Create `frontend/vitest.config.ts`:

```ts
import path from "node:path";
import { defineConfig } from "vitest/config";

export default defineConfig({
  test: {
    environment: "jsdom",
    globals: true,
    setupFiles: ["./src/test/setup.ts"],
  },
  resolve: {
    alias: {
      "@": path.resolve(__dirname, "./src"),
    },
  },
});
```

Create `frontend/src/test/setup.ts`:

```ts
import "@testing-library/jest-dom/vitest";
import { cleanup } from "@testing-library/react";
import { afterEach, vi } from "vitest";

afterEach(() => {
  cleanup();
  localStorage.clear();
  sessionStorage.clear();
  vi.restoreAllMocks();
  vi.unstubAllEnvs();
});
```

Install:

```bash
npm.cmd install
```

- [ ] **Step 2: Write the failing auth-storage tests**

Create `frontend/src/lib/__tests__/auth-storage.test.ts`:

```ts
import {
  clearAuthSession,
  readAuthSession,
  saveAuthSession,
} from "@/lib/auth-storage";
import type { AuthSession } from "@/lib/contracts";
import { describe, expect, it } from "vitest";

const session: AuthSession = {
  token: "token-123",
  user: {
    id: "user-1",
    email: "member@example.com",
    status: "active",
    profile: {
      display_name: "member",
    },
  },
};

describe("auth-storage", () => {
  it("returns null when no session is stored", () => {
    expect(readAuthSession()).toBeNull();
  });

  it("persists and restores the auth session", () => {
    saveAuthSession(session);

    expect(readAuthSession()).toEqual(session);
  });

  it("clears the stored session", () => {
    saveAuthSession(session);
    clearAuthSession();

    expect(readAuthSession()).toBeNull();
  });
});
```

- [ ] **Step 3: Run the storage tests and verify they fail because the modules do not exist yet**

Run:

```bash
npm.cmd run test -- src/lib/__tests__/auth-storage.test.ts
```

Expected:

```text
FAIL  src/lib/__tests__/auth-storage.test.ts
Error: Failed to resolve import "@/lib/auth-storage"
```

- [ ] **Step 4: Implement the shared contracts and auth-storage helpers**

Create `frontend/src/lib/contracts.ts`:

```ts
export type ApiProfile = {
  display_name: string | null;
};

export type ApiUser = {
  id: string;
  email: string;
  status: string;
  profile?: ApiProfile | null;
};

export type AuthSession = {
  token: string;
  user: ApiUser;
};

export type AuthResponse = AuthSession;

export type Publication = {
  id: string;
  title: string;
  text: string;
  author_id: string;
  created_at: string;
  updated_at: string;
  media_type?: string | null;
  author?: ApiUser | null;
};

export type PaginatedResponse<T> = {
  current_page: number;
  data: T[];
  last_page: number;
  per_page: number;
  total: number;
};
```

Create `frontend/src/lib/auth-storage.ts`:

```ts
import type { AuthSession } from "@/lib/contracts";

const STORAGE_KEY = "ihmproj.auth";

export function readAuthSession(): AuthSession | null {
  const raw = localStorage.getItem(STORAGE_KEY);

  if (!raw) {
    return null;
  }

  try {
    return JSON.parse(raw) as AuthSession;
  } catch {
    localStorage.removeItem(STORAGE_KEY);
    return null;
  }
}

export function saveAuthSession(session: AuthSession): void {
  localStorage.setItem(STORAGE_KEY, JSON.stringify(session));
}

export function clearAuthSession(): void {
  localStorage.removeItem(STORAGE_KEY);
}
```

- [ ] **Step 5: Re-run the storage tests and verify they pass**

Run:

```bash
npm.cmd run test -- src/lib/__tests__/auth-storage.test.ts
```

Expected:

```text
PASS  src/lib/__tests__/auth-storage.test.ts
Tests: 3 passed
```

- [ ] **Step 6: Commit the frontend test harness and storage primitives**

```bash
git add package.json package-lock.json vitest.config.ts src/test/setup.ts src/lib/contracts.ts src/lib/auth-storage.ts src/lib/__tests__/auth-storage.test.ts
git commit -m "test: add frontend session storage primitives"
```

## Task 3: Add the Shared API Client with Explicit Bearer-Token Behavior

**Files:**
- Create: `frontend/.env.example`
- Create: `frontend/src/lib/api.ts`
- Create: `frontend/src/lib/__tests__/api.test.ts`

- [ ] **Step 1: Write the failing API client tests**

Create `frontend/src/lib/__tests__/api.test.ts`:

```ts
import { ApiError, createApiClient } from "@/lib/api";
import { beforeEach, describe, expect, it, vi } from "vitest";

describe("api client", () => {
  beforeEach(() => {
    vi.stubEnv("NEXT_PUBLIC_API_BASE_URL", "http://127.0.0.1:8000/api");
    vi.stubGlobal("fetch", vi.fn());
  });

  it("sends public publication requests without an authorization header", async () => {
    vi.mocked(fetch).mockResolvedValueOnce(
      new Response(JSON.stringify({ data: [] }), { status: 200 }),
    );

    const api = createApiClient();
    await api.listPublications();

    expect(fetch).toHaveBeenCalledWith(
      "http://127.0.0.1:8000/api/publications",
      expect.objectContaining({
        headers: expect.objectContaining({
          Accept: "application/json",
        }),
      }),
    );
  });

  it("attaches the bearer token on authenticated publication writes", async () => {
    vi.mocked(fetch).mockResolvedValueOnce(
      new Response(JSON.stringify({ id: "pub-1", title: "Created", text: "Body", author_id: "user-1", created_at: "", updated_at: "" }), {
        status: 201,
      }),
    );

    const api = createApiClient();
    await api.createPublication("token-123", {
      title: "Created",
      text: "Body",
    });

    expect(fetch).toHaveBeenCalledWith(
      "http://127.0.0.1:8000/api/publications",
      expect.objectContaining({
        method: "POST",
        headers: expect.objectContaining({
          Authorization: "Bearer token-123",
          "Content-Type": "application/json",
        }),
      }),
    );
  });

  it("raises an ApiError with the Laravel message for failed requests", async () => {
    vi.mocked(fetch).mockResolvedValueOnce(
      new Response(JSON.stringify({ message: "Forbidden." }), { status: 403 }),
    );

    const api = createApiClient();

    await expect(api.deletePublication("token-123", "pub-1")).rejects.toEqual(
      new ApiError(403, "Forbidden."),
    );
  });
});
```

- [ ] **Step 2: Run the API client tests and verify they fail**

Run:

```bash
npm.cmd run test -- src/lib/__tests__/api.test.ts
```

Expected:

```text
FAIL  src/lib/__tests__/api.test.ts
Error: Failed to resolve import "@/lib/api"
```

- [ ] **Step 3: Implement the environment contract and typed API client**

Create `frontend/.env.example`:

```dotenv
NEXT_PUBLIC_API_BASE_URL=http://127.0.0.1:8000/api
```

Create `frontend/src/lib/api.ts`:

```ts
import type { AuthResponse, PaginatedResponse, Publication } from "@/lib/contracts";

type PublicationPayload = {
  title: string;
  text: string;
  category_id?: string | null;
  media_type?: string | null;
};

export class ApiError extends Error {
  constructor(
    public readonly status: number,
    message: string,
  ) {
    super(message);
  }
}

function getApiBaseUrl(): string {
  const value = process.env.NEXT_PUBLIC_API_BASE_URL;

  if (!value) {
    throw new Error("Missing NEXT_PUBLIC_API_BASE_URL");
  }

  return value.replace(/\/$/, "");
}

async function request<T>(
  path: string,
  init: RequestInit = {},
  token?: string,
): Promise<T> {
  const response = await fetch(`${getApiBaseUrl()}${path}`, {
    ...init,
    headers: {
      Accept: "application/json",
      ...(init.body ? { "Content-Type": "application/json" } : {}),
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...init.headers,
    },
  });

  const payload = (await response.json().catch(() => null)) as
    | { message?: string }
    | T
    | null;

  if (!response.ok) {
    throw new ApiError(
      response.status,
      payload && "message" in payload && payload.message ? payload.message : "Request failed.",
    );
  }

  return payload as T;
}

export function createApiClient() {
  return {
    register(input: { email: string; password: string; password_confirmation: string }) {
      return request<AuthResponse>("/auth/register", {
        method: "POST",
        body: JSON.stringify(input),
      });
    },
    login(input: { email: string; password: string }) {
      return request<AuthResponse>("/auth/login", {
        method: "POST",
        body: JSON.stringify(input),
      });
    },
    logout(token: string) {
      return request<{ message: string }>("/auth/logout", {
        method: "POST",
      }, token);
    },
    getCurrentUser(token: string) {
      return request<AuthResponse["user"]>("/auth/me", {}, token);
    },
    listPublications() {
      return request<PaginatedResponse<Publication>>("/publications");
    },
    getPublication(publicationId: string) {
      return request<Publication>(`/publications/${publicationId}`);
    },
    createPublication(token: string, input: PublicationPayload) {
      return request<Publication>("/publications", {
        method: "POST",
        body: JSON.stringify(input),
      }, token);
    },
    updatePublication(token: string, publicationId: string, input: PublicationPayload) {
      return request<Publication>(`/publications/${publicationId}`, {
        method: "PATCH",
        body: JSON.stringify(input),
      }, token);
    },
    deletePublication(token: string, publicationId: string) {
      return request<{ message: string }>(`/publications/${publicationId}`, {
        method: "DELETE",
      }, token);
    },
  };
}
```

- [ ] **Step 4: Re-run the API client tests and verify they pass**

Run:

```bash
npm.cmd run test -- src/lib/__tests__/api.test.ts
```

Expected:

```text
PASS  src/lib/__tests__/api.test.ts
Tests: 3 passed
```

- [ ] **Step 5: Commit the API client layer**

```bash
git add .env.example src/lib/api.ts src/lib/__tests__/api.test.ts
git commit -m "feat: add frontend api client for auth and publications"
```

## Task 4: Add the Auth Provider and Restore Sessions on App Load

**Files:**
- Create: `frontend/src/components/auth/auth-provider.tsx`
- Create: `frontend/src/components/auth/use-auth.ts`
- Create: `frontend/src/components/auth/__tests__/auth-provider.test.tsx`
- Modify: `frontend/src/app/layout.tsx`

- [ ] **Step 1: Write the failing auth-provider tests**

Create `frontend/src/components/auth/__tests__/auth-provider.test.tsx`:

```tsx
import { AuthProvider } from "@/components/auth/auth-provider";
import { useAuth } from "@/components/auth/use-auth";
import { createApiClient } from "@/lib/api";
import * as storage from "@/lib/auth-storage";
import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";

vi.mock("@/lib/api", () => ({
  createApiClient: vi.fn(),
}));

function Probe() {
  const auth = useAuth();

  return (
    <div>
      <span data-testid="status">{auth.status}</span>
      <span data-testid="email">{auth.user?.email ?? "none"}</span>
    </div>
  );
}

describe("AuthProvider", () => {
  it("restores a stored session by calling /auth/me", async () => {
    vi.spyOn(storage, "readAuthSession").mockReturnValue({
      token: "token-123",
      user: {
        id: "stale",
        email: "stale@example.com",
        status: "active",
        profile: null,
      },
    });

    vi.mocked(createApiClient).mockReturnValue({
      getCurrentUser: vi.fn().mockResolvedValue({
        id: "user-1",
        email: "member@example.com",
        status: "active",
        profile: { display_name: "member" },
      }),
    } as never);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>,
    );

    await waitFor(() => {
      expect(screen.getByTestId("status")).toHaveTextContent("authenticated");
      expect(screen.getByTestId("email")).toHaveTextContent("member@example.com");
    });
  });

  it("drops back to guest when there is no stored session", async () => {
    vi.spyOn(storage, "readAuthSession").mockReturnValue(null);
    vi.mocked(createApiClient).mockReturnValue({} as never);

    render(
      <AuthProvider>
        <Probe />
      </AuthProvider>,
    );

    await waitFor(() => {
      expect(screen.getByTestId("status")).toHaveTextContent("guest");
    });
  });
});
```

- [ ] **Step 2: Run the provider tests and verify they fail**

Run:

```bash
npm.cmd run test -- src/components/auth/__tests__/auth-provider.test.tsx
```

Expected:

```text
FAIL  src/components/auth/__tests__/auth-provider.test.tsx
Error: Failed to resolve import "@/components/auth/auth-provider"
```

- [ ] **Step 3: Implement the auth provider, hook, and root layout wiring**

Create `frontend/src/components/auth/auth-provider.tsx`:

```tsx
"use client";

import { createContext, useEffect, useMemo, useState } from "react";
import { createApiClient } from "@/lib/api";
import {
  clearAuthSession,
  readAuthSession,
  saveAuthSession,
} from "@/lib/auth-storage";
import type { ApiUser } from "@/lib/contracts";

type AuthStatus = "loading" | "guest" | "authenticated";

type AuthContextValue = {
  status: AuthStatus;
  token: string | null;
  user: ApiUser | null;
  login: (input: { email: string; password: string }) => Promise<void>;
  register: (input: { email: string; password: string; password_confirmation: string }) => Promise<void>;
  logout: () => Promise<void>;
};

export const AuthContext = createContext<AuthContextValue | null>(null);

const api = createApiClient();

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [status, setStatus] = useState<AuthStatus>("loading");
  const [token, setToken] = useState<string | null>(null);
  const [user, setUser] = useState<ApiUser | null>(null);

  useEffect(() => {
    const stored = readAuthSession();

    if (!stored) {
      setStatus("guest");
      return;
    }

    setToken(stored.token);

    api.getCurrentUser(stored.token)
      .then((currentUser) => {
        saveAuthSession({ token: stored.token, user: currentUser });
        setToken(stored.token);
        setUser(currentUser);
        setStatus("authenticated");
      })
      .catch(() => {
        clearAuthSession();
        setToken(null);
        setUser(null);
        setStatus("guest");
      });
  }, []);

  async function login(input: { email: string; password: string }) {
    const session = await api.login(input);
    saveAuthSession(session);
    setToken(session.token);
    setUser(session.user);
    setStatus("authenticated");
  }

  async function register(input: { email: string; password: string; password_confirmation: string }) {
    const session = await api.register(input);
    saveAuthSession(session);
    setToken(session.token);
    setUser(session.user);
    setStatus("authenticated");
  }

  async function logout() {
    const currentToken = token;

    clearAuthSession();
    setToken(null);
    setUser(null);
    setStatus("guest");

    if (currentToken) {
      await api.logout(currentToken).catch(() => undefined);
    }
  }

  const value = useMemo(
    () => ({ status, token, user, login, register, logout }),
    [status, token, user],
  );

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}
```

Create `frontend/src/components/auth/use-auth.ts`:

```ts
"use client";

import { useContext } from "react";
import { AuthContext } from "@/components/auth/auth-provider";

export function useAuth() {
  const value = useContext(AuthContext);

  if (!value) {
    throw new Error("useAuth must be used within AuthProvider");
  }

  return value;
}
```

Update `frontend/src/app/layout.tsx`:

```tsx
import type { Metadata } from "next";
import { Inter, Manrope } from "next/font/google";
import { AuthProvider } from "@/components/auth/auth-provider";
import "./globals.css";

// font declarations unchanged

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html
      lang="en"
      className={`${inter.variable} ${manrope.variable} h-full antialiased`}
    >
      <head>
        <link
          href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&display=optional"
          rel="stylesheet"
        />
      </head>
      <body className="min-h-full flex flex-col bg-surface text-on-surface">
        <AuthProvider>{children}</AuthProvider>
      </body>
    </html>
  );
}
```

- [ ] **Step 4: Re-run the provider tests and verify they pass**

Run:

```bash
npm.cmd run test -- src/components/auth/__tests__/auth-provider.test.tsx
```

Expected:

```text
PASS  src/components/auth/__tests__/auth-provider.test.tsx
Tests: 2 passed
```

- [ ] **Step 5: Commit the auth provider layer**

```bash
git add src/components/auth/auth-provider.tsx src/components/auth/use-auth.ts src/components/auth/__tests__/auth-provider.test.tsx src/app/layout.tsx
git commit -m "feat: add frontend auth provider and session restore"
```

## Task 5: Replace the Mock Auth Pages and Navigation with Real Auth-Aware UI

**Files:**
- Create: `frontend/src/components/auth/auth-shell.tsx`
- Create: `frontend/src/components/auth/login-form.tsx`
- Create: `frontend/src/components/auth/register-form.tsx`
- Create: `frontend/src/components/auth/__tests__/auth-forms.test.tsx`
- Modify: `frontend/src/app/(auth)/login/page.tsx`
- Create: `frontend/src/app/(auth)/register/page.tsx`
- Modify: `frontend/src/components/Navigation/TopNavBar.tsx`
- Modify: `frontend/src/components/Navigation/SideNavBar.tsx`

- [ ] **Step 1: Write the failing auth-form tests**

Create `frontend/src/components/auth/__tests__/auth-forms.test.tsx`:

```tsx
import { LoginForm } from "@/components/auth/login-form";
import { RegisterForm } from "@/components/auth/register-form";
import { useAuth } from "@/components/auth/use-auth";
import userEvent from "@testing-library/user-event";
import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";

const push = vi.fn();

vi.mock("next/navigation", () => ({
  useRouter: () => ({ push }),
  useSearchParams: () => new URLSearchParams("next=/create"),
}));

vi.mock("@/components/auth/use-auth", () => ({
  useAuth: vi.fn(),
}));

describe("auth forms", () => {
  it("submits login credentials and redirects to the next route", async () => {
    const login = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useAuth).mockReturnValue({
      status: "guest",
      token: null,
      user: null,
      login,
      register: vi.fn(),
      logout: vi.fn(),
    });

    render(<LoginForm />);

    await userEvent.type(screen.getByLabelText(/email/i), "member@example.com");
    await userEvent.type(screen.getByLabelText(/password/i), "password123");
    await userEvent.click(screen.getByRole("button", { name: /log in/i }));

    await waitFor(() => {
      expect(login).toHaveBeenCalledWith({
        email: "member@example.com",
        password: "password123",
      });
      expect(push).toHaveBeenCalledWith("/create");
    });
  });

  it("submits registration data including password confirmation", async () => {
    const register = vi.fn().mockResolvedValue(undefined);
    vi.mocked(useAuth).mockReturnValue({
      status: "guest",
      token: null,
      user: null,
      login: vi.fn(),
      register,
      logout: vi.fn(),
    });

    render(<RegisterForm />);

    await userEvent.type(screen.getByLabelText(/email/i), "new@example.com");
    await userEvent.type(screen.getByLabelText(/^password$/i), "password123");
    await userEvent.type(screen.getByLabelText(/confirm password/i), "password123");
    await userEvent.click(screen.getByRole("button", { name: /create account/i }));

    await waitFor(() => {
      expect(register).toHaveBeenCalledWith({
        email: "new@example.com",
        password: "password123",
        password_confirmation: "password123",
      });
      expect(push).toHaveBeenCalledWith("/create");
    });
  });
});
```

- [ ] **Step 2: Run the auth-form tests and verify they fail**

Run:

```bash
npm.cmd run test -- src/components/auth/__tests__/auth-forms.test.tsx
```

Expected:

```text
FAIL  src/components/auth/__tests__/auth-forms.test.tsx
Error: Failed to resolve import "@/components/auth/login-form"
```

- [ ] **Step 3: Implement the shared auth shell, real forms, and auth-aware navigation**

Create `frontend/src/components/auth/auth-shell.tsx`:

```tsx
import Link from "next/link";

export function AuthShell({
  title,
  subtitle,
  mode,
  children,
}: {
  title: string;
  subtitle: string;
  mode: "login" | "register";
  children: React.ReactNode;
}) {
  return (
    <div className="bg-surface text-on-surface min-h-screen flex items-center justify-center p-6">
      <main className="w-full max-w-4xl rounded-2xl bg-surface-container-lowest shadow-2xl overflow-hidden grid lg:grid-cols-[1.2fr_1fr]">
        <section className="hidden lg:flex flex-col justify-between bg-slate-900 text-white p-10">
          <div>
            <p className="text-xs uppercase tracking-[0.3em] text-primary-fixed">Sovereign Ledger</p>
            <h1 className="mt-6 text-4xl font-extrabold headline">Anonymous discourse with a real API behind it.</h1>
          </div>
          <p className="text-sm text-slate-300">Guest feed access is public. Publishing requires an authenticated account.</p>
        </section>
        <section className="p-8 md:p-12">
          <nav className="mb-8 flex gap-2 rounded-full bg-surface-container-low p-1 w-fit">
            <Link
              href="/login"
              className={`rounded-full px-5 py-2 text-sm font-semibold ${mode === "login" ? "bg-surface-container-lowest text-primary shadow-sm" : "text-on-surface-variant"}`}
            >
              Login
            </Link>
            <Link
              href="/register"
              className={`rounded-full px-5 py-2 text-sm font-semibold ${mode === "register" ? "bg-surface-container-lowest text-primary shadow-sm" : "text-on-surface-variant"}`}
            >
              Sign Up
            </Link>
          </nav>
          <header className="mb-8">
            <h2 className="text-3xl font-bold headline">{title}</h2>
            <p className="mt-2 text-on-surface-variant">{subtitle}</p>
          </header>
          {children}
        </section>
      </main>
    </div>
  );
}
```

Create `frontend/src/components/auth/login-form.tsx`:

```tsx
"use client";

import { useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function LoginForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const { login } = useAuth();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [pending, setPending] = useState(false);

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setPending(true);
    setError(null);

    try {
      await login({ email, password });
      router.push(searchParams.get("next") ?? "/");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "Login failed.");
    } finally {
      setPending(false);
    }
  }

  return (
    <form className="space-y-5" onSubmit={onSubmit}>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="email">Email</label>
        <input id="email" type="email" value={email} onChange={(event) => setEmail(event.target.value)} className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none" required />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="password">Password</label>
        <input id="password" type="password" value={password} onChange={(event) => setPassword(event.target.value)} className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none" required />
      </div>
      {error ? <p className="text-sm text-error">{error}</p> : null}
      <button type="submit" disabled={pending} className="w-full rounded-xl bg-primary py-3 font-bold text-white disabled:opacity-60">
        {pending ? "Logging in..." : "Log In"}
      </button>
    </form>
  );
}
```

Create `frontend/src/components/auth/register-form.tsx`:

```tsx
"use client";

import { useState } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function RegisterForm() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const { register } = useAuth();
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [passwordConfirmation, setPasswordConfirmation] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [pending, setPending] = useState(false);

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setPending(true);
    setError(null);

    try {
      await register({
        email,
        password,
        password_confirmation: passwordConfirmation,
      });
      router.push(searchParams.get("next") ?? "/");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "Registration failed.");
    } finally {
      setPending(false);
    }
  }

  return (
    <form className="space-y-5" onSubmit={onSubmit}>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="register-email">Email</label>
        <input id="register-email" type="email" value={email} onChange={(event) => setEmail(event.target.value)} className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none" required />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="register-password">Password</label>
        <input id="register-password" type="password" value={password} onChange={(event) => setPassword(event.target.value)} className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none" required />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="register-password-confirmation">Confirm Password</label>
        <input id="register-password-confirmation" type="password" value={passwordConfirmation} onChange={(event) => setPasswordConfirmation(event.target.value)} className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none" required />
      </div>
      {error ? <p className="text-sm text-error">{error}</p> : null}
      <button type="submit" disabled={pending} className="w-full rounded-xl bg-primary py-3 font-bold text-white disabled:opacity-60">
        {pending ? "Creating account..." : "Create Account"}
      </button>
    </form>
  );
}
```

Update `frontend/src/app/(auth)/login/page.tsx`:

```tsx
import { AuthShell } from "@/components/auth/auth-shell";
import { LoginForm } from "@/components/auth/login-form";

export default function LoginPage() {
  return (
    <AuthShell
      mode="login"
      title="Welcome back"
      subtitle="Log in with the Laravel account you already have."
    >
      <LoginForm />
    </AuthShell>
  );
}
```

Create `frontend/src/app/(auth)/register/page.tsx`:

```tsx
import { AuthShell } from "@/components/auth/auth-shell";
import { RegisterForm } from "@/components/auth/register-form";

export default function RegisterPage() {
  return (
    <AuthShell
      mode="register"
      title="Create your account"
      subtitle="Registration should immediately authenticate the new user."
    >
      <RegisterForm />
    </AuthShell>
  );
}
```

Update `frontend/src/components/Navigation/TopNavBar.tsx`:

```tsx
"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function TopNavBar() {
  const pathname = usePathname();
  const { status, user, logout } = useAuth();
  const isAuthenticated = status === "authenticated";

  return (
    <nav className="fixed top-0 w-full z-50 bg-slate-50/80 backdrop-blur-xl shadow-sm flex justify-between items-center px-8 h-16">
      <div className="flex items-center gap-8">
        <Link href="/" className="text-2xl font-bold tracking-tight text-slate-900 headline">
          Sovereign Ledger
        </Link>
        <div className="hidden md:flex items-center gap-6">
          <Link href="/" className={pathname === "/" ? "text-blue-700 font-bold border-b-2 border-blue-700 text-sm tracking-wider pb-1" : "text-slate-600 font-medium hover:text-blue-600 text-sm tracking-wider pb-1"}>
            Feed
          </Link>
        </div>
      </div>

      <div className="flex items-center gap-4">
        {isAuthenticated ? (
          <>
            <span className="hidden md:inline text-sm text-slate-600">
              {user?.profile?.display_name ?? user?.email}
            </span>
            <Link href="/create" className="bg-primary-gradient text-white px-5 py-2 rounded-xl text-xs font-bold tracking-wide shadow-lg shadow-primary/20">
              Create Post
            </Link>
            <button type="button" onClick={() => void logout()} className="text-slate-600 font-medium hover:text-blue-600 transition-colors px-4 py-2 text-sm">
              Logout
            </button>
          </>
        ) : (
          <>
            <Link href="/login" className="text-slate-600 font-medium hover:text-blue-600 transition-colors px-4 py-2 text-sm">
              Login
            </Link>
            <Link href="/register" className="bg-primary-gradient text-white px-5 py-2 rounded-xl text-xs font-bold tracking-wide shadow-lg shadow-primary/20">
              Sign Up
            </Link>
          </>
        )}
      </div>
    </nav>
  );
}
```

Update `frontend/src/components/Navigation/SideNavBar.tsx`:

```tsx
"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function SideNavBar() {
  const pathname = usePathname();
  const { status } = useAuth();

  const navItems = [
    { name: "Home", href: "/", icon: "home" },
    ...(status === "authenticated"
      ? [{ name: "Create", href: "/create", icon: "add_circle" }]
      : []),
  ];

  return (
    <aside className="hidden lg:flex flex-col p-4 gap-2 h-[calc(100vh-4rem)] w-64 fixed left-0 top-16 bg-slate-100/50 backdrop-blur-xl z-40 shadow-sm border-r border-outline-variant/10">
      <div className="mb-6 px-4 py-2 mt-4">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
            <span className="material-symbols-outlined text-primary">forum</span>
          </div>
          <div>
            <p className="text-lg font-black text-slate-900 leading-tight headline">The Ledger</p>
            <p className="text-xs text-slate-500 font-medium tracking-widest mt-1">Public Feed</p>
          </div>
        </div>
      </div>

      <div className="flex flex-col gap-1 flex-1">
        {navItems.map((item) => (
          <Link
            key={item.name}
            href={item.href}
            className={`flex items-center gap-3 px-4 py-3 rounded-xl transition-all duration-200 ${
              pathname === item.href
                ? "bg-white/70 text-blue-700 font-semibold shadow-sm"
                : "text-slate-500 hover:bg-slate-200/50 hover:text-slate-700"
            }`}
          >
            <span className="material-symbols-outlined">{item.icon}</span>
            <span className="text-sm font-medium">{item.name}</span>
          </Link>
        ))}
      </div>
    </aside>
  );
}
```

- [ ] **Step 4: Re-run the auth-form tests and verify they pass**

Run:

```bash
npm.cmd run test -- src/components/auth/__tests__/auth-forms.test.tsx
```

Expected:

```text
PASS  src/components/auth/__tests__/auth-forms.test.tsx
Tests: 2 passed
```

- [ ] **Step 5: Commit the real auth UI**

```bash
git add src/components/auth/auth-shell.tsx src/components/auth/login-form.tsx src/components/auth/register-form.tsx src/components/auth/__tests__/auth-forms.test.tsx src/app/\(auth\)/login/page.tsx src/app/\(auth\)/register/page.tsx src/components/Navigation/TopNavBar.tsx src/components/Navigation/SideNavBar.tsx
git commit -m "feat: wire real auth pages and navigation"
```

## Task 6: Replace the Static Feed with Real Public Publication Data

**Files:**
- Delete: `frontend/src/app/page.tsx`
- Create: `frontend/src/components/publications/publication-card.tsx`
- Create: `frontend/src/components/publications/publication-feed.tsx`
- Create: `frontend/src/components/publications/__tests__/publication-feed.test.tsx`
- Modify: `frontend/src/app/(main)/page.tsx`

- [ ] **Step 1: Write the failing publication-feed tests**

Create `frontend/src/components/publications/__tests__/publication-feed.test.tsx`:

```tsx
import { PublicationFeed } from "@/components/publications/publication-feed";
import { useAuth } from "@/components/auth/use-auth";
import { createApiClient } from "@/lib/api";
import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";

vi.mock("@/lib/api", () => ({
  createApiClient: vi.fn(),
}));

vi.mock("@/components/auth/use-auth", () => ({
  useAuth: vi.fn(),
}));

describe("PublicationFeed", () => {
  it("renders fetched publications for guests", async () => {
    vi.mocked(useAuth).mockReturnValue({
      status: "guest",
      token: null,
      user: null,
      login: vi.fn(),
      register: vi.fn(),
      logout: vi.fn(),
    });

    vi.mocked(createApiClient).mockReturnValue({
      listPublications: vi.fn().mockResolvedValue({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 1,
        data: [
          {
            id: "pub-1",
            title: "Visible post",
            text: "Post body",
            author_id: "author-1",
            created_at: "2026-04-21T00:00:00Z",
            updated_at: "2026-04-21T00:00:00Z",
            author: {
              id: "author-1",
              email: "author@example.com",
              status: "active",
              profile: { display_name: "author" },
            },
          },
        ],
      }),
    } as never);

    render(<PublicationFeed />);

    expect(screen.getByText(/loading publications/i)).toBeInTheDocument();

    await waitFor(() => {
      expect(screen.getByText("Visible post")).toBeInTheDocument();
      expect(screen.queryByText(/edit/i)).not.toBeInTheDocument();
    });
  });

  it("shows ownership actions when the current user owns the post", async () => {
    vi.mocked(useAuth).mockReturnValue({
      status: "authenticated",
      token: "token-123",
      user: {
        id: "author-1",
        email: "author@example.com",
        status: "active",
        profile: { display_name: "author" },
      },
      login: vi.fn(),
      register: vi.fn(),
      logout: vi.fn(),
    });

    vi.mocked(createApiClient).mockReturnValue({
      listPublications: vi.fn().mockResolvedValue({
        current_page: 1,
        last_page: 1,
        per_page: 20,
        total: 1,
        data: [
          {
            id: "pub-1",
            title: "Owned post",
            text: "Post body",
            author_id: "author-1",
            created_at: "2026-04-21T00:00:00Z",
            updated_at: "2026-04-21T00:00:00Z",
          },
        ],
      }),
    } as never);

    render(<PublicationFeed />);

    await waitFor(() => {
      expect(screen.getByRole("link", { name: /edit/i })).toBeInTheDocument();
      expect(screen.getByRole("button", { name: /delete/i })).toBeInTheDocument();
    });
  });
});
```

- [ ] **Step 2: Run the feed tests and verify they fail**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-feed.test.tsx
```

Expected:

```text
FAIL  src/components/publications/__tests__/publication-feed.test.tsx
Error: Failed to resolve import "@/components/publications/publication-feed"
```

- [ ] **Step 3: Implement the feed card, feed loader, and root page cleanup**

Create `frontend/src/components/publications/publication-card.tsx`:

```tsx
import Link from "next/link";
import type { Publication } from "@/lib/contracts";

export function PublicationCard({
  publication,
  canManage,
  onDelete,
}: {
  publication: Publication;
  canManage: boolean;
  onDelete: (publicationId: string) => void;
}) {
  return (
    <article className="bg-surface-container-lowest rounded-xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
      <div className="flex items-center justify-between mb-4">
        <div>
          <p className="text-sm font-bold text-on-surface">
            {publication.author?.profile?.display_name ?? publication.author?.email ?? "Anonymous"}
          </p>
          <p className="text-xs text-on-surface-variant">{new Date(publication.created_at).toLocaleString()}</p>
        </div>
        {canManage ? (
          <div className="flex items-center gap-3">
            <Link href={`/publications/${publication.id}/edit`} className="text-sm font-semibold text-primary">
              Edit
            </Link>
            <button type="button" onClick={() => onDelete(publication.id)} className="text-sm font-semibold text-error">
              Delete
            </button>
          </div>
        ) : null}
      </div>
      <h2 className="text-2xl font-bold mb-3 headline">{publication.title}</h2>
      <p className="text-on-surface-variant leading-relaxed">{publication.text}</p>
    </article>
  );
}
```

Create `frontend/src/components/publications/publication-feed.tsx`:

```tsx
"use client";

import { useEffect, useMemo, useState } from "react";
import { useAuth } from "@/components/auth/use-auth";
import { ApiError, createApiClient } from "@/lib/api";
import type { Publication } from "@/lib/contracts";
import { PublicationCard } from "@/components/publications/publication-card";

const api = createApiClient();

export function PublicationFeed() {
  const { user, status, token } = useAuth();
  const [publications, setPublications] = useState<Publication[]>([]);
  const [state, setState] = useState<"loading" | "ready" | "empty" | "error">("loading");
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    api.listPublications()
      .then((response) => {
        setPublications(response.data);
        setState(response.data.length === 0 ? "empty" : "ready");
      })
      .catch((caught) => {
        setError(caught instanceof Error ? caught.message : "Failed to load publications.");
        setState("error");
      });
  }, []);

  const ownership = useMemo(
    () => new Set(publications.filter((publication) => publication.author_id === user?.id).map((publication) => publication.id)),
    [publications, user?.id],
  );

  async function deletePublication(publicationId: string) {
    if (status !== "authenticated" || !token) {
      return;
    }

    if (!window.confirm("Delete this publication?")) {
      return;
    }

    try {
      await api.deletePublication(token, publicationId);
      setPublications((current) => current.filter((publication) => publication.id !== publicationId));
      setState((current) => (current === "ready" && publications.length === 1 ? "empty" : current));
    } catch (caught) {
      if (caught instanceof ApiError) {
        setError(caught.message);
        setState("error");
      }
    }
  }

  if (state === "loading") {
    return <p className="mx-auto max-w-4xl p-8 text-on-surface-variant">Loading publications...</p>;
  }

  if (state === "error") {
    return <p className="mx-auto max-w-4xl p-8 text-error">{error}</p>;
  }

  if (state === "empty") {
    return <p className="mx-auto max-w-4xl p-8 text-on-surface-variant">No publications yet.</p>;
  }

  return (
    <div className="max-w-4xl mx-auto flex flex-col gap-8">
      {publications.map((publication) => (
        <PublicationCard
          key={publication.id}
          publication={publication}
          canManage={ownership.has(publication.id)}
          onDelete={deletePublication}
        />
      ))}
    </div>
  );
}
```

Update `frontend/src/app/(main)/page.tsx`:

```tsx
import { PublicationFeed } from "@/components/publications/publication-feed";

export default function HomePage() {
  return (
    <>
      <main className="flex-1 p-8">
        <header className="max-w-4xl mx-auto mb-12">
          <h1 className="text-5xl font-extrabold tracking-tight text-on-surface mb-4 headline">Public Feed</h1>
          <p className="text-lg text-on-surface-variant max-w-2xl leading-relaxed font-body">
            Guests can read visible publications. Authenticated users can publish and manage their own posts.
          </p>
        </header>
        <PublicationFeed />
      </main>
    </>
  );
}
```

Delete `frontend/src/app/page.tsx`.

- [ ] **Step 4: Re-run the feed tests and verify they pass**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-feed.test.tsx
```

Expected:

```text
PASS  src/components/publications/__tests__/publication-feed.test.tsx
Tests: 2 passed
```

- [ ] **Step 5: Commit the real feed**

```bash
git add src/components/publications/publication-card.tsx src/components/publications/publication-feed.tsx src/components/publications/__tests__/publication-feed.test.tsx src/app/\(main\)/page.tsx src/app/page.tsx
git commit -m "feat: render public publication feed from backend"
```

## Task 7: Add the Shared Publication Editor and Auth-Only Create Flow

**Files:**
- Create: `frontend/src/components/publications/require-auth.tsx`
- Create: `frontend/src/components/publications/publication-editor.tsx`
- Create: `frontend/src/components/publications/__tests__/publication-editor.test.tsx`
- Modify: `frontend/src/app/(main)/create/page.tsx`

- [ ] **Step 1: Write the failing publication-editor tests for create mode**

Create `frontend/src/components/publications/__tests__/publication-editor.test.tsx`:

```tsx
import { PublicationEditor } from "@/components/publications/publication-editor";
import { useAuth } from "@/components/auth/use-auth";
import { createApiClient } from "@/lib/api";
import userEvent from "@testing-library/user-event";
import { render, screen, waitFor } from "@testing-library/react";
import { describe, expect, it, vi } from "vitest";

const push = vi.fn();

vi.mock("next/navigation", () => ({
  useRouter: () => ({ push, replace: push }),
  useParams: () => ({ publicationId: "pub-1" }),
}));

vi.mock("@/components/auth/use-auth", () => ({
  useAuth: vi.fn(),
}));

vi.mock("@/lib/api", () => ({
  createApiClient: vi.fn(),
}));

describe("PublicationEditor", () => {
  it("creates a publication for an authenticated user", async () => {
    vi.mocked(useAuth).mockReturnValue({
      status: "authenticated",
      token: "token-123",
      user: {
        id: "author-1",
        email: "author@example.com",
        status: "active",
        profile: { display_name: "author" },
      },
      login: vi.fn(),
      register: vi.fn(),
      logout: vi.fn(),
    });

    vi.mocked(createApiClient).mockReturnValue({
      createPublication: vi.fn().mockResolvedValue({
        id: "pub-1",
        title: "Created title",
        text: "Created body",
        author_id: "author-1",
        created_at: "",
        updated_at: "",
      }),
    } as never);

    render(<PublicationEditor mode="create" />);

    await userEvent.type(screen.getByLabelText(/title/i), "Created title");
    await userEvent.type(screen.getByLabelText(/body/i), "Created body");
    await userEvent.click(screen.getByRole("button", { name: /publish/i }));

    await waitFor(() => {
      expect(push).toHaveBeenCalledWith("/");
    });
  });
});
```

- [ ] **Step 2: Run the publication-editor tests and verify they fail**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-editor.test.tsx
```

Expected:

```text
FAIL  src/components/publications/__tests__/publication-editor.test.tsx
Error: Failed to resolve import "@/components/publications/publication-editor"
```

- [ ] **Step 3: Implement the auth guard, shared editor, and create page**

Create `frontend/src/components/publications/require-auth.tsx`:

```tsx
"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function RequireAuth({
  children,
  nextPath,
}: {
  children: React.ReactNode;
  nextPath: string;
}) {
  const router = useRouter();
  const { status } = useAuth();

  useEffect(() => {
    if (status === "guest") {
      router.replace(`/login?next=${encodeURIComponent(nextPath)}`);
    }
  }, [nextPath, router, status]);

  if (status === "loading") {
    return <p className="p-8 text-on-surface-variant">Checking session...</p>;
  }

  if (status === "guest") {
    return null;
  }

  return <>{children}</>;
}
```

Create `frontend/src/components/publications/publication-editor.tsx`:

```tsx
"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";
import { createApiClient } from "@/lib/api";

const api = createApiClient();

export function PublicationEditor({ mode }: { mode: "create" | "edit" }) {
  const router = useRouter();
  const params = useParams<{ publicationId: string }>();
  const { token } = useAuth();
  const [title, setTitle] = useState("");
  const [text, setText] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [pending, setPending] = useState(false);
  const [loading, setLoading] = useState(mode === "edit");

  useEffect(() => {
    if (mode !== "edit") {
      return;
    }

    api.getPublication(params.publicationId)
      .then((publication) => {
        setTitle(publication.title);
        setText(publication.text);
        setLoading(false);
      })
      .catch((caught) => {
        setError(caught instanceof Error ? caught.message : "Failed to load publication.");
        setLoading(false);
      });
  }, [mode, params.publicationId]);

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (!token) {
      setError("You must be logged in to publish.");
      return;
    }

    setPending(true);
    setError(null);

    try {
      if (mode === "create") {
        await api.createPublication(token, { title, text });
      } else {
        await api.updatePublication(token, params.publicationId, { title, text });
      }

      router.push("/");
    } catch (caught) {
      setError(caught instanceof Error ? caught.message : "Publication save failed.");
    } finally {
      setPending(false);
    }
  }

  if (loading) {
    return <p className="text-on-surface-variant">Loading publication...</p>;
  }

  return (
    <form className="space-y-8" onSubmit={onSubmit}>
      <div className="space-y-2">
        <label className="text-sm font-bold uppercase tracking-wider text-on-surface-variant" htmlFor="publication-title">
          Title
        </label>
        <input id="publication-title" value={title} onChange={(event) => setTitle(event.target.value)} className="w-full rounded-xl border-2 border-surface-variant/30 bg-surface-container-low px-4 py-4 text-xl font-headline font-bold outline-none" required />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-bold uppercase tracking-wider text-on-surface-variant" htmlFor="publication-body">
          Body
        </label>
        <textarea id="publication-body" value={text} onChange={(event) => setText(event.target.value)} className="w-full rounded-xl border-2 border-surface-variant/30 bg-surface-container-low p-4 outline-none" rows={8} required />
      </div>
      {error ? <p className="text-sm text-error">{error}</p> : null}
      <button type="submit" disabled={pending} className="rounded-2xl bg-primary-gradient px-8 py-4 text-lg font-extrabold text-white disabled:opacity-60">
        {pending ? "Saving..." : mode === "create" ? "Publish" : "Save Changes"}
      </button>
    </form>
  );
}
```

Update `frontend/src/app/(main)/create/page.tsx`:

```tsx
import { PublicationEditor } from "@/components/publications/publication-editor";
import { RequireAuth } from "@/components/publications/require-auth";

export default function CreatePostPage() {
  return (
    <div className="max-w-5xl mx-auto p-8">
      <RequireAuth nextPath="/create">
        <header className="mb-10">
          <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-3 headline">
            Create a Publication
          </h1>
          <p className="text-on-surface-variant font-body text-lg max-w-xl leading-relaxed">
            Publish a new entry through the Laravel publication API.
          </p>
        </header>
        <PublicationEditor mode="create" />
      </RequireAuth>
    </div>
  );
}
```

- [ ] **Step 4: Re-run the publication-editor tests and verify create mode passes**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-editor.test.tsx
```

Expected:

```text
PASS  src/components/publications/__tests__/publication-editor.test.tsx
Tests: 1 passed
```

- [ ] **Step 5: Commit the create-publication flow**

```bash
git add src/components/publications/require-auth.tsx src/components/publications/publication-editor.tsx src/components/publications/__tests__/publication-editor.test.tsx src/app/\(main\)/create/page.tsx
git commit -m "feat: add authenticated publication creation flow"
```

## Task 8: Add Edit and Delete Publication Flows

**Files:**
- Modify: `frontend/src/components/publications/publication-editor.tsx`
- Modify: `frontend/src/components/publications/publication-feed.tsx`
- Create: `frontend/src/app/(main)/publications/[publicationId]/edit/page.tsx`
- Modify: `frontend/src/components/publications/__tests__/publication-editor.test.tsx`
- Modify: `frontend/src/components/publications/__tests__/publication-feed.test.tsx`

- [ ] **Step 1: Extend the failing editor and feed tests for edit and delete**

Append to `frontend/src/components/publications/__tests__/publication-editor.test.tsx`:

```tsx
it("loads an existing publication and submits an update", async () => {
  vi.mocked(useAuth).mockReturnValue({
    status: "authenticated",
    token: "token-123",
    user: {
      id: "author-1",
      email: "author@example.com",
      status: "active",
      profile: { display_name: "author" },
    },
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
  });

  vi.mocked(createApiClient).mockReturnValue({
    getPublication: vi.fn().mockResolvedValue({
      id: "pub-1",
      title: "Original title",
      text: "Original body",
      author_id: "author-1",
      created_at: "",
      updated_at: "",
    }),
    updatePublication: vi.fn().mockResolvedValue({
      id: "pub-1",
      title: "Updated title",
      text: "Updated body",
      author_id: "author-1",
      created_at: "",
      updated_at: "",
    }),
  } as never);

  render(<PublicationEditor mode="edit" />);

  expect(await screen.findByDisplayValue("Original title")).toBeInTheDocument();
  await userEvent.clear(screen.getByLabelText(/title/i));
  await userEvent.type(screen.getByLabelText(/title/i), "Updated title");
  await userEvent.click(screen.getByRole("button", { name: /save changes/i }));

  await waitFor(() => {
    expect(push).toHaveBeenCalledWith("/");
  });
});
```

Append to `frontend/src/components/publications/__tests__/publication-feed.test.tsx`:

```tsx
it("removes a publication after confirming delete", async () => {
  vi.spyOn(window, "confirm").mockReturnValue(true);

  vi.mocked(useAuth).mockReturnValue({
    status: "authenticated",
    token: "token-123",
    user: {
      id: "author-1",
      email: "author@example.com",
      status: "active",
      profile: { display_name: "author" },
    },
    login: vi.fn(),
    register: vi.fn(),
    logout: vi.fn(),
  });

  vi.mocked(createApiClient).mockReturnValue({
    listPublications: vi.fn().mockResolvedValue({
      current_page: 1,
      last_page: 1,
      per_page: 20,
      total: 1,
      data: [
        {
          id: "pub-1",
          title: "Owned post",
          text: "Post body",
          author_id: "author-1",
          created_at: "2026-04-21T00:00:00Z",
          updated_at: "2026-04-21T00:00:00Z",
        },
      ],
    }),
    deletePublication: vi.fn().mockResolvedValue({ message: "Publication deleted." }),
  } as never);

  render(<PublicationFeed />);

  expect(await screen.findByText("Owned post")).toBeInTheDocument();
  await userEvent.click(screen.getByRole("button", { name: /delete/i }));

  await waitFor(() => {
    expect(screen.queryByText("Owned post")).not.toBeInTheDocument();
  });
});
```

- [ ] **Step 2: Run the editor and feed tests and verify the new cases fail**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-editor.test.tsx src/components/publications/__tests__/publication-feed.test.tsx
```

Expected:

```text
FAIL  src/components/publications/__tests__/publication-editor.test.tsx > loads an existing publication and submits an update
FAIL  src/components/publications/__tests__/publication-feed.test.tsx > removes a publication after confirming delete
```

- [ ] **Step 3: Implement the edit route and finish the delete/update flow**

Update `frontend/src/components/publications/publication-feed.tsx` so the delete handler computes the next state from the current array:

```tsx
      await api.deletePublication(token, publicationId);
      setPublications((current) => {
        const next = current.filter((publication) => publication.id !== publicationId);
        setState(next.length === 0 ? "empty" : "ready");
        return next;
      });
```

Create `frontend/src/app/(main)/publications/[publicationId]/edit/page.tsx`:

```tsx
import { PublicationEditor } from "@/components/publications/publication-editor";
import { RequireAuth } from "@/components/publications/require-auth";

export default function EditPublicationPage({
  params,
}: {
  params: { publicationId: string };
}) {
  return (
    <div className="max-w-5xl mx-auto p-8">
      <RequireAuth nextPath={`/publications/${params.publicationId}/edit`}>
        <header className="mb-10">
          <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-3 headline">
            Edit Publication
          </h1>
          <p className="text-on-surface-variant font-body text-lg max-w-xl leading-relaxed">
            Update your existing publication and save it back through the Laravel API.
          </p>
        </header>
        <PublicationEditor mode="edit" />
      </RequireAuth>
    </div>
  );
}
```

- [ ] **Step 4: Re-run the editor and feed tests and verify create, edit, and delete all pass**

Run:

```bash
npm.cmd run test -- src/components/publications/__tests__/publication-editor.test.tsx src/components/publications/__tests__/publication-feed.test.tsx
```

Expected:

```text
PASS  src/components/publications/__tests__/publication-editor.test.tsx
PASS  src/components/publications/__tests__/publication-feed.test.tsx
Tests: 5 passed
```

- [ ] **Step 5: Commit the edit/delete publication flow**

```bash
git add src/components/publications/publication-editor.tsx src/components/publications/publication-feed.tsx src/components/publications/__tests__/publication-editor.test.tsx src/components/publications/__tests__/publication-feed.test.tsx src/app/\(main\)/publications/\[publicationId\]/edit/page.tsx
git commit -m "feat: add publication edit and delete flows"
```

## Task 9: Clean the Existing Lint Blockers and Run Full Verification

**Files:**
- Modify: `frontend/src/app/(main)/dashboard/page.tsx`
- Modify: `frontend/src/app/(main)/moderation/page.tsx`

- [ ] **Step 1: Fix the current JSX quote errors that block `npm run lint`**

Update `frontend/src/app/(main)/dashboard/page.tsx`:

```tsx
<p className="text-xs font-bold">{"Filter \"Election\" updated"}</p>
<p className="text-[11px] text-on-surface-variant line-clamp-2 italic">
  {"\"I have 5 years experience moderating decentralized communities and understanding...\""}
</p>
```

Update `frontend/src/app/(main)/moderation/page.tsx`:

```tsx
<h4 className="text-lg font-bold mb-2 font-headline">
  {"\"The financial system is a house of cards, we must dismantle it by force tonight at...\""}
</h4>
<p className="text-on-surface-variant text-sm leading-relaxed mb-4 font-body">
  {"This user is repeatedly calling for physical disruption of infrastructure in the #Economics thread. Multiple flags for 'Incitement of Violence'."}
</p>
<p className="text-sm font-medium italic font-body">
  {"\"Does anyone have reliable data on the new tax protocol?\""}
</p>
<h4 className="text-md font-bold mb-2 font-headline">
  {"Reported Comment: \"You're a complete idiot for even asking that. Go back to your hole, nobody wants you here.\""}
</h4>
<h4 className="text-md font-bold mb-2 font-headline">
  {"\"FREE TOKENS!!! CLICK LINK NOW TO CLAIM YOUR AIRDROP!!! [REDACTED LINK]\""}
</h4>
```

- [ ] **Step 2: Run the frontend linter and verify it no longer reports errors**

Run:

```bash
npm.cmd run lint
```

Expected:

```text
No ESLint errors
```

- [ ] **Step 3: Run the backend and frontend automated verification suites**

Run:

```bash
php artisan test
```

Expected:

```text
PASS  Tests\Feature\PublicationApiTest
PASS  Tests\Feature\ExampleTest
PASS  Tests\Unit\ExampleTest
```

Run:

```bash
npm.cmd run test
```

Expected:

```text
PASS  src/lib/__tests__/auth-storage.test.ts
PASS  src/lib/__tests__/api.test.ts
PASS  src/components/auth/__tests__/auth-provider.test.tsx
PASS  src/components/auth/__tests__/auth-forms.test.tsx
PASS  src/components/publications/__tests__/publication-feed.test.tsx
PASS  src/components/publications/__tests__/publication-editor.test.tsx
```

Run:

```bash
npm.cmd run build
```

Expected:

```text
Compiled successfully
```

If this step fails only because `next/font/google` cannot reach Google Fonts, rerun it on a machine with network access. The build currently depends on remote font downloads from `src/app/layout.tsx`.

- [ ] **Step 4: Run the manual smoke test**

Execute this sequence in the browser:

1. Open `/` as a guest and confirm the feed renders.
2. Open `/create` as a guest and confirm the app redirects to `/login?next=/create`.
3. Register a new account and confirm the app redirects back to `/create` or `/`.
4. Create a publication and confirm it appears in the feed.
5. Refresh the page and confirm the session is restored.
6. Edit the publication and confirm the new title/body are visible.
7. Delete the publication and confirm it disappears from the feed.
8. Log out and confirm create/edit/delete actions disappear.

- [ ] **Step 5: Commit the verification cleanup**

```bash
git add src/app/\(main\)/dashboard/page.tsx src/app/\(main\)/moderation/page.tsx
git commit -m "chore: clear frontend lint blockers for integration verification"
```
