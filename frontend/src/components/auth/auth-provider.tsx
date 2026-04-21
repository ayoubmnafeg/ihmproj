"use client";

import { createContext, useEffect, useState } from "react";
import { createApiClient } from "@/lib/api";
import {
  clearAuthSession,
  readAuthSession,
  saveAuthSession,
} from "@/lib/auth-storage";
import type { ApiUser } from "@/lib/contracts";

type AuthStatus = "loading" | "guest" | "authenticated";

export type AuthContextValue = {
  status: AuthStatus;
  token: string | null;
  user: ApiUser | null;
  login: (input: { email: string; password: string }) => Promise<void>;
  register: (input: {
    email: string;
    password: string;
    password_confirmation: string;
  }) => Promise<void>;
  logout: () => Promise<void>;
};

export const AuthContext = createContext<AuthContextValue | null>(null);

export function AuthProvider({ children }: { children: React.ReactNode }) {
  const [status, setStatus] = useState<AuthStatus>("loading");
  const [token, setToken] = useState<string | null>(null);
  const [user, setUser] = useState<ApiUser | null>(null);

  useEffect(() => {
    const api = createApiClient();
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
    const api = createApiClient();
    const session = await api.login(input);
    saveAuthSession(session);
    setToken(session.token);
    setUser(session.user);
    setStatus("authenticated");
  }

  async function register(input: {
    email: string;
    password: string;
    password_confirmation: string;
  }) {
    const api = createApiClient();
    const session = await api.register(input);
    saveAuthSession(session);
    setToken(session.token);
    setUser(session.user);
    setStatus("authenticated");
  }

  async function logout() {
    const api = createApiClient();
    const currentToken = token;

    clearAuthSession();
    setToken(null);
    setUser(null);
    setStatus("guest");

    if (currentToken) {
      await api.logout(currentToken).catch(() => undefined);
    }
  }

  return (
    <AuthContext.Provider
      value={{ status, token, user, login, register, logout }}
    >
      {children}
    </AuthContext.Provider>
  );
}
