"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";

export function SideNavBar() {
  const pathname = usePathname();

  const navItems = [
    { name: "Home", href: "/", icon: "home" },
    { name: "Categories", href: "/categories", icon: "grid_view" },
    { name: "Bookmarks", href: "/bookmarks", icon: "bookmark" },
    { name: "Moderation", href: "/moderation", icon: "gavel" },
    { name: "Admin Panel", href: "/dashboard", icon: "admin_panel_settings" },
  ];

  return (
    <aside className="hidden lg:flex flex-col p-4 gap-2 h-[calc(100vh-4rem)] w-64 fixed left-0 top-16 bg-slate-100/50 backdrop-blur-xl z-40 shadow-sm border-r border-outline-variant/10">
      <div className="mb-6 px-4 py-2 mt-4">
        <div className="flex items-center gap-3">
          <div className="w-10 h-10 rounded-full bg-primary-container flex items-center justify-center">
            <span className="material-symbols-outlined text-primary">
              account_balance
            </span>
          </div>
          <div>
            <p className="text-lg font-black text-slate-900 leading-tight headline">The Ledger</p>
            <p className="text-xs text-slate-500 font-medium tracking-widest mt-1">Governance Portal</p>
          </div>
        </div>
      </div>

      <div className="flex flex-col gap-1 flex-1">
        {navItems.map((item) => {
          const isActive = pathname === item.href;
          return (
            <Link
              key={item.name}
              href={item.href}
              className={`flex items-center gap-3 px-4 py-3 rounded-xl hover:-translate-y-[1px] hover:translate-x-1 transition-all duration-200 ${
                isActive
                  ? "bg-white/70 text-blue-700 font-semibold shadow-sm"
                  : "text-slate-500 hover:bg-slate-200/50 hover:text-slate-700"
              }`}
            >
              <span className="material-symbols-outlined">{item.icon}</span>
              <span className="text-sm font-medium">{item.name}</span>
            </Link>
          );
        })}
      </div>

      <div className="mt-auto pt-6 border-t border-transparent flex flex-col gap-1">
        <button className="w-full bg-surface-container text-on-surface text-xs font-bold uppercase tracking-wider rounded-xl py-3 px-4 flex items-center justify-between gap-2 mb-4 hover:shadow-sm transition-all">
          System Status
          <span className="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
        </button>
        <Link
          href="/settings"
          className="flex items-center gap-3 px-4 py-2 text-slate-500 hover:bg-slate-200/50 hover:text-slate-700 rounded-xl transition-all"
        >
          <span className="material-symbols-outlined">settings</span>
          <span className="text-sm font-medium">Settings</span>
        </Link>
        <Link
          href="/support"
          className="flex items-center gap-3 px-4 py-2 text-slate-500 hover:bg-slate-200/50 hover:text-slate-700 rounded-xl transition-all"
        >
          <span className="material-symbols-outlined">help</span>
          <span className="text-sm font-medium">Support</span>
        </Link>
      </div>
    </aside>
  );
}
