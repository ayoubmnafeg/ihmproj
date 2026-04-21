import type {
  AuthResponse,
  PaginatedResponse,
  Publication,
} from "@/lib/contracts";

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
    this.name = "ApiError";
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
      payload && "message" in payload && payload.message
        ? payload.message
        : "Request failed.",
    );
  }

  return payload as T;
}

export function createApiClient() {
  return {
    register(input: {
      email: string;
      password: string;
      password_confirmation: string;
    }) {
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
      return request<{ message: string }>(
        "/auth/logout",
        {
          method: "POST",
        },
        token,
      );
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
      return request<Publication>(
        "/publications",
        {
          method: "POST",
          body: JSON.stringify(input),
        },
        token,
      );
    },
    updatePublication(
      token: string,
      publicationId: string,
      input: PublicationPayload,
    ) {
      return request<Publication>(
        `/publications/${publicationId}`,
        {
          method: "PATCH",
          body: JSON.stringify(input),
        },
        token,
      );
    },
    deletePublication(token: string, publicationId: string) {
      return request<{ message: string }>(
        `/publications/${publicationId}`,
        {
          method: "DELETE",
        },
        token,
      );
    },
  };
}
