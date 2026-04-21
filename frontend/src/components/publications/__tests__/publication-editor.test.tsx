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
