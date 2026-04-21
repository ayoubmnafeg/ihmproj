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
      new Response(
        JSON.stringify({
          id: "pub-1",
          title: "Created",
          text: "Body",
          author_id: "user-1",
          created_at: "",
          updated_at: "",
        }),
        { status: 201 },
      ),
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
