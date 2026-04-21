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
