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
      expect(screen.getByTestId("email")).toHaveTextContent(
        "member@example.com",
      );
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
