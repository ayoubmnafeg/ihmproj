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
      expect(
        screen.getByRole("button", { name: /delete/i }),
      ).toBeInTheDocument();
    });
  });
});
