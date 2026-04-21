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
      setError(
        caught instanceof Error ? caught.message : "Registration failed.",
      );
    } finally {
      setPending(false);
    }
  }

  return (
    <form className="space-y-5" onSubmit={onSubmit}>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="register-email">
          Email
        </label>
        <input
          id="register-email"
          type="email"
          value={email}
          onChange={(event) => setEmail(event.target.value)}
          className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none"
          required
        />
      </div>
      <div className="space-y-2">
        <label className="text-sm font-semibold" htmlFor="register-password">
          Password
        </label>
        <input
          id="register-password"
          type="password"
          value={password}
          onChange={(event) => setPassword(event.target.value)}
          className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none"
          required
        />
      </div>
      <div className="space-y-2">
        <label
          className="text-sm font-semibold"
          htmlFor="register-password-confirmation"
        >
          Confirm Password
        </label>
        <input
          id="register-password-confirmation"
          type="password"
          value={passwordConfirmation}
          onChange={(event) => setPasswordConfirmation(event.target.value)}
          className="w-full rounded-xl border border-outline-variant bg-surface-container-low px-4 py-3 outline-none"
          required
        />
      </div>
      {error ? <p className="text-sm text-error">{error}</p> : null}
      <button
        type="submit"
        disabled={pending}
        className="w-full rounded-xl bg-primary py-3 font-bold text-white disabled:opacity-60"
      >
        {pending ? "Creating account..." : "Create Account"}
      </button>
    </form>
  );
}
