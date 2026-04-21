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
