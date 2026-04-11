"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

export function TopNavBar() {
  const pathname = usePathname();

  const isExplore = pathname === "/explore" || pathname === "/";
  const isTrending = pathname === "/trending";
  const isLatest = pathname === "/latest";

  return (
    <nav className="fixed top-0 w-full z-50 bg-slate-50/80 backdrop-blur-xl shadow-sm flex justify-between items-center px-8 h-16">
      <div className="flex items-center gap-8">
        <Link href="/" className="text-2xl font-bold tracking-tight text-slate-900 headline">
          Sovereign Ledger
        </Link>
        <div className="hidden md:flex items-center gap-6">
          <Link
            href="/"
            className={`${
              isExplore ? "text-blue-700 font-bold border-b-2 border-blue-700" : "text-slate-600 font-medium hover:text-blue-600"
            } text-sm tracking-wider pb-1 transition-colors`}
          >
            Explore
          </Link>
          <Link
            href="/trending"
            className={`${
              isTrending ? "text-blue-700 font-bold border-b-2 border-blue-700" : "text-slate-600 font-medium hover:text-blue-600"
            } text-sm tracking-wider pb-1 transition-colors`}
          >
            Trending
          </Link>
          <Link
            href="/latest"
            className={`${
              isLatest ? "text-blue-700 font-bold border-b-2 border-blue-700" : "text-slate-600 font-medium hover:text-blue-600"
            } text-sm tracking-wider pb-1 transition-colors`}
          >
            Latest
          </Link>
        </div>
      </div>

      <div className="flex-1 max-w-md mx-8">
        <div className="relative flex items-center">
          <span className="material-symbols-outlined absolute left-3 text-slate-400">
            search
          </span>
          <input
            className="w-full bg-surface-container-high border-none rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-primary/20 text-sm outline-none"
            placeholder="Search the ledger..."
            type="text"
          />
        </div>
      </div>

      <div className="flex items-center gap-4">
        <button className="material-symbols-outlined text-slate-500 hover:text-primary transition-colors">
          notifications
        </button>
        <Link href="/profile" className="material-symbols-outlined text-slate-500 hover:text-primary transition-colors">
          shield_person
        </Link>
        <div className="h-6 w-[1px] bg-outline-variant/30 mx-2"></div>
        <Link href="/login" className="text-slate-600 font-medium hover:text-blue-600 transition-colors px-4 py-2 text-sm">
          Login
        </Link>
        <Link href="/create" className="bg-primary-gradient text-white px-5 py-2 rounded-xl text-xs font-bold tracking-wide shadow-lg shadow-primary/20 hover:scale-95 transition-all">
          Create Post
        </Link>
      </div>
    </nav>
  );
}
