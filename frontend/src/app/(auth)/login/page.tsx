import Link from "next/link";

export default function LoginPage() {
  return (
    <div className="bg-surface text-on-surface min-h-screen flex items-center justify-center p-6 selection:bg-primary-container selection:text-on-primary-container">
      {/* The Auth Canvas */}
      <main className="w-full max-w-6xl grid grid-cols-1 lg:grid-cols-12 gap-0 overflow-hidden rounded-xl shadow-2xl bg-surface-container-lowest">
        
        {/* Branding & Context Side (Visual) */}
        <section className="lg:col-span-7 relative hidden lg:flex flex-col justify-between p-12 overflow-hidden bg-slate-900 min-h-[600px]">
          <div className="absolute inset-0 z-0">
            <img 
              className="w-full h-full object-cover opacity-40 mix-blend-luminosity" 
              alt="Abstract architectural structure" 
              src="https://lh3.googleusercontent.com/aida-public/AB6AXuBsGmmYgrktVo5WlVZSGiuVyqLPo5mybZ92GZ4bnpF3nCoX9JYKu7va0LQFtO8hHTj7MLeOE-q1s4aBCugz2omfpQV9198O1p5ODYpjjVdX089-JZVeGy4Tfp98X3HIJvz2_GW-Qpq9tASQWTHFAQkZeTZJdigWFjkn7cnIXCBvJbxMeRdLm4kYfhV-9Cqw9bbQyvargadoSNSxTds3wgp5lwqlnQU1JwbNxbja1kbmDZBCwgx47PfShMf3EUHPWpG8P5XjbRFyMjGS"
            />
            <div className="absolute inset-0 bg-gradient-to-br from-primary-dim/40 to-slate-950/80"></div>
          </div>
          
          <div className="relative z-10">
            <div className="flex items-center gap-3">
              <span className="material-symbols-outlined text-primary-fixed text-4xl">shield_person</span>
              <h1 className="text-3xl font-extrabold tracking-tight text-white headline">Sovereign Ledger</h1>
            </div>
          </div>
          
          <div className="relative z-10 space-y-6">
            <h2 className="text-5xl font-extrabold text-white leading-tight headline">
              Privacy is the <br/>
              <span className="text-primary-fixed">Foundation of Discourse.</span>
            </h2>
            <p className="text-lg text-slate-300 max-w-md font-body leading-relaxed">
              Join an elite community of anonymous thinkers. No tracking, no profiling, just pure governance and architectural transparency.
            </p>
            <div className="flex items-center gap-8 pt-4">
              <div className="flex flex-col">
                <span className="text-2xl font-bold text-white">4.2k+</span>
                <span className="text-sm text-slate-400 font-label">Active Nodes</span>
              </div>
              <div className="flex flex-col">
                <span className="text-2xl font-bold text-white">100%</span>
                <span className="text-sm text-slate-400 font-label">Encrypted</span>
              </div>
            </div>
          </div>
          
          <footer className="relative z-10">
            <p className="text-xs text-slate-500 font-label tracking-widest uppercase">© 2024 Sovereign Ledger Protocol</p>
          </footer>
        </section>

        {/* Form Side */}
        <section className="lg:col-span-5 flex flex-col p-8 md:p-16 justify-center bg-surface-container-lowest">
          
          {/* Logo for Mobile */}
          <div className="lg:hidden flex items-center gap-2 mb-12">
            <span className="material-symbols-outlined text-primary text-3xl">shield_person</span>
            <span className="text-xl font-bold tracking-tight text-on-surface headline">Sovereign Ledger</span>
          </div>

          {/* Tab Switcher */}
          <nav className="flex p-1 bg-surface-container-low rounded-full mb-10 w-fit self-center lg:self-start">
            <button className="px-8 py-2.5 rounded-full text-sm font-semibold transition-all bg-surface-container-lowest text-primary shadow-sm">
              Login
            </button>
            <button className="px-8 py-2.5 rounded-full text-sm font-medium text-on-surface-variant hover:text-on-surface transition-all">
              Sign Up
            </button>
          </nav>

          <div className="space-y-2 mb-10">
            <h3 className="text-3xl font-bold text-on-surface headline">Welcome back</h3>
            <p className="text-on-surface-variant font-body">Enter your credentials to access the ledger.</p>
          </div>

          <form className="space-y-6" action="/">
            {/* Username Field */}
            <div className="space-y-2">
              <label className="block text-xs font-semibold text-on-surface-variant tracking-wider uppercase font-label" htmlFor="username">Anonymous Alias</label>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <span className="material-symbols-outlined text-outline text-xl">person</span>
                </div>
                <input 
                  className="w-full pl-11 pr-4 py-4 bg-surface-container-low border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-xl transition-all font-body text-on-surface placeholder:text-outline/50 outline-none" 
                  id="username" 
                  name="username" 
                  placeholder="e.g. GhostProtocol_99" 
                  type="text"
                />
              </div>
            </div>

            {/* Password Field */}
            <div className="space-y-2">
              <div className="flex justify-between items-center">
                <label className="block text-xs font-semibold text-on-surface-variant tracking-wider uppercase font-label" htmlFor="password">Private Key</label>
                <a className="text-xs font-semibold text-primary hover:text-primary-dim transition-colors font-label" href="#">Forgot access?</a>
              </div>
              <div className="relative">
                <div className="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                  <span className="material-symbols-outlined text-outline text-xl">lock</span>
                </div>
                <input 
                  className="w-full pl-11 pr-4 py-4 bg-surface-container-low border-b-2 border-transparent focus:border-primary focus:ring-0 rounded-xl transition-all font-body text-on-surface placeholder:text-outline/50 outline-none" 
                  id="password" 
                  name="password" 
                  placeholder="••••••••••••" 
                  type="password"
                />
              </div>
            </div>

            {/* Security Persistence */}
            <div className="flex items-center">
              <input 
                className="w-5 h-5 rounded border-surface-variant text-primary focus:ring-primary/20 bg-surface-container-low" 
                id="remember" 
                type="checkbox"
              />
              <label className="ml-3 text-sm text-on-surface-variant font-body" htmlFor="remember">Trust this device for 30 days</label>
            </div>

            {/* Action Button */}
            <button 
              className="w-full py-4 bg-gradient-to-br from-primary to-primary-dim text-on-primary font-bold rounded-xl shadow-lg shadow-primary/20 hover:shadow-primary/30 active:scale-[0.98] transition-all headline text-lg" 
              type="submit"
            >
              Verify Identity
            </button>
          </form>

          <div className="mt-12 space-y-6">
            <div className="relative">
              <div aria-hidden="true" className="absolute inset-0 flex items-center">
                <div className="w-full border-t border-surface-variant"></div>
              </div>
              <div className="relative flex justify-center text-xs font-semibold uppercase tracking-widest">
                <span className="bg-surface-container-lowest px-4 text-outline font-label">Alternative Auth</span>
              </div>
            </div>

            <div className="grid grid-cols-2 gap-4">
              <button className="flex items-center justify-center gap-3 py-3 border border-outline-variant/30 rounded-xl hover:bg-surface-container-low transition-all">
                <span className="material-symbols-outlined text-on-surface">key</span>
                <span className="text-sm font-semibold text-on-surface font-label">Passkey</span>
              </button>
              <button className="flex items-center justify-center gap-3 py-3 border border-outline-variant/30 rounded-xl hover:bg-surface-container-low transition-all">
                <span className="material-symbols-outlined text-on-surface">token</span>
                <span className="text-sm font-semibold text-on-surface font-label">Web3</span>
              </button>
            </div>
          </div>

          <div className="mt-10 text-center">
            <p className="text-sm text-on-surface-variant font-body">
              By accessing the ledger, you agree to our{" "}
              <Link className="text-primary font-semibold hover:underline" href="#">Moderation Policy</Link>
            </p>
          </div>
        </section>
      </main>
    </div>
  );
}
