import Link from "next/link";

export function AuthShell({
  title,
  subtitle,
  mode,
  children,
}: {
  title: string;
  subtitle: string;
  mode: "login" | "register";
  children: React.ReactNode;
}) {
  return (
    <div className="bg-surface text-on-surface min-h-screen flex items-center justify-center p-6">
      <main className="w-full max-w-4xl rounded-2xl bg-surface-container-lowest shadow-2xl overflow-hidden grid lg:grid-cols-[1.2fr_1fr]">
        <section className="hidden lg:flex flex-col justify-between bg-slate-900 text-white p-10">
          <div>
            <p className="text-xs uppercase tracking-[0.3em] text-primary-fixed">
              Sovereign Ledger
            </p>
            <h1 className="mt-6 text-4xl font-extrabold headline">
              Anonymous discourse with a real API behind it.
            </h1>
          </div>
          <p className="text-sm text-slate-300">
            Guest feed access is public. Publishing requires an authenticated
            account.
          </p>
        </section>
        <section className="p-8 md:p-12">
          <nav className="mb-8 flex gap-2 rounded-full bg-surface-container-low p-1 w-fit">
            <Link
              href="/login"
              className={`rounded-full px-5 py-2 text-sm font-semibold ${
                mode === "login"
                  ? "bg-surface-container-lowest text-primary shadow-sm"
                  : "text-on-surface-variant"
              }`}
            >
              Login
            </Link>
            <Link
              href="/register"
              className={`rounded-full px-5 py-2 text-sm font-semibold ${
                mode === "register"
                  ? "bg-surface-container-lowest text-primary shadow-sm"
                  : "text-on-surface-variant"
              }`}
            >
              Sign Up
            </Link>
          </nav>
          <header className="mb-8">
            <h2 className="text-3xl font-bold headline">{title}</h2>
            <p className="mt-2 text-on-surface-variant">{subtitle}</p>
          </header>
          {children}
        </section>
      </main>
    </div>
  );
}
