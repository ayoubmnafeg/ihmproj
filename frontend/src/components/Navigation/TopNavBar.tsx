"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";

export function TopNavBar() {
  const pathname = usePathname();
  const { status, user, logout } = useAuth();
  const isAuthenticated = status === "authenticated";

  return (
    <nav className="fixed top-0 w-full z-50 bg-slate-50/80 backdrop-blur-xl shadow-sm flex justify-between items-center px-8 h-16">
      <div className="flex items-center gap-8">
        <Link
          href="/"
          className="text-2xl font-bold tracking-tight text-slate-900 headline"
        >
          Sovereign Ledger
        </Link>
        <div className="hidden md:flex items-center gap-6">
          <Link
            href="/"
            className={`${
              pathname === "/"
                ? "text-blue-700 font-bold border-b-2 border-blue-700"
                : "text-slate-600 font-medium hover:text-blue-600"
            } text-sm tracking-wider pb-1 transition-colors`}
          >
            Feed
          </Link>
        </div>
      </div>

      <div className="flex items-center gap-4">
        {isAuthenticated ? (
          <>
            <span className="hidden md:inline text-sm text-slate-600">
              {user?.profile?.display_name ?? user?.email}
            </span>
            <Link
              href="/create"
              className="bg-primary-gradient text-white px-5 py-2 rounded-xl text-xs font-bold tracking-wide shadow-lg shadow-primary/20"
            >
              Create Post
            </Link>
            <button
              type="button"
              onClick={() => void logout()}
              className="text-slate-600 font-medium hover:text-blue-600 transition-colors px-4 py-2 text-sm"
            >
              Logout
            </button>
          </>
        ) : (
          <>
            <Link
              href="/login"
              className="text-slate-600 font-medium hover:text-blue-600 transition-colors px-4 py-2 text-sm"
            >
              Login
            </Link>
            <Link
              href="/register"
              className="bg-primary-gradient text-white px-5 py-2 rounded-xl text-xs font-bold tracking-wide shadow-lg shadow-primary/20"
            >
              Sign Up
            </Link>
          </>
        )}
      </div>
    </nav>
  );
}
