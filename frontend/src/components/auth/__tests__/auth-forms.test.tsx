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

    await userEvent.type(
      screen.getByLabelText(/email/i),
      "member@example.com",
    );
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
    await userEvent.type(
      screen.getByLabelText(/^password$/i),
      "password123",
    );
    await userEvent.type(
      screen.getByLabelText(/confirm password/i),
      "password123",
    );
    await userEvent.click(
      screen.getByRole("button", { name: /create account/i }),
    );

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
