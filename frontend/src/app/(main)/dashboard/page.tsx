export default function AdminDashboardPage() {
  return (
    <div className="max-w-7xl mx-auto space-y-8">
      {/* Header Section */}
      <section className="flex flex-col md:flex-row md:items-end justify-between gap-4">
        <div>
          <p className="text-primary font-bold text-xs uppercase tracking-[0.2em] mb-2 font-label">Platform Overview</p>
          <h1 className="text-4xl font-extrabold text-on-surface leading-tight headline">System Sovereignty</h1>
        </div>
        <div className="flex gap-2">
          <button className="bg-surface-container-high px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 text-on-surface hover:bg-surface-variant transition-colors">
            <span className="material-symbols-outlined text-sm">calendar_today</span>
            Last 30 Days
          </button>
          <button className="bg-surface-container-high px-4 py-2 rounded-xl text-sm font-semibold flex items-center gap-2 text-on-surface hover:bg-surface-variant transition-colors">
            <span className="material-symbols-outlined text-sm">download</span>
            Report
          </button>
        </div>
      </section>

      {/* Metrics Bento Grid */}
      <section className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        {/* Stat Card 1 */}
        <div className="bg-surface-container-lowest p-6 rounded-xl space-y-4 shadow-sm border border-surface-container-low/50">
          <div className="flex justify-between items-start">
            <div className="p-2 bg-primary-container/30 rounded-lg">
              <span className="material-symbols-outlined text-primary">group</span>
            </div>
            <span className="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+12.5%</span>
          </div>
          <div>
            <p className="text-sm font-medium text-on-surface-variant">Active Anons</p>
            <h3 className="text-2xl font-extrabold mt-1 headline">42,892</h3>
          </div>
        </div>

        {/* Stat Card 2 */}
        <div className="bg-surface-container-lowest p-6 rounded-xl space-y-4 shadow-sm border border-surface-container-low/50">
          <div className="flex justify-between items-start">
            <div className="p-2 bg-tertiary-container/30 rounded-lg">
              <span className="material-symbols-outlined text-tertiary">forum</span>
            </div>
            <span className="text-green-500 text-xs font-bold bg-green-50 px-2 py-1 rounded-full">+8.2%</span>
          </div>
          <div>
            <p className="text-sm font-medium text-on-surface-variant">Daily Threads</p>
            <h3 className="text-2xl font-extrabold mt-1 headline">1,402</h3>
          </div>
        </div>

        {/* Stat Card 3 */}
        <div className="bg-surface-container-lowest p-6 rounded-xl space-y-4 shadow-sm border border-surface-container-low/50">
          <div className="flex justify-between items-start">
            <div className="p-2 bg-error-container/20 rounded-lg">
              <span className="material-symbols-outlined text-error">report</span>
            </div>
            <span className="text-error text-xs font-bold bg-error-container/10 px-2 py-1 rounded-full">+24%</span>
          </div>
          <div>
            <p className="text-sm font-medium text-on-surface-variant">Pending Flags</p>
            <h3 className="text-2xl font-extrabold mt-1 headline">158</h3>
          </div>
        </div>

        {/* Stat Card 4 */}
        <div className="bg-surface-container-lowest p-6 rounded-xl space-y-4 shadow-sm border border-surface-container-low/50">
          <div className="flex justify-between items-start">
            <div className="p-2 bg-secondary-container/50 rounded-lg">
              <span className="material-symbols-outlined text-secondary">security</span>
            </div>
            <span className="text-slate-500 text-xs font-bold bg-slate-100 px-2 py-1 rounded-full">Stable</span>
          </div>
          <div>
            <p className="text-sm font-medium text-on-surface-variant">Filter Hits</p>
            <h3 className="text-2xl font-extrabold mt-1 headline">12,401</h3>
          </div>
        </div>
      </section>

      {/* Main Analytics Area */}
      <section className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Large Chart Placeholder (Tonal Layering) */}
        <div className="lg:col-span-2 bg-surface-container-lowest p-8 rounded-xl min-h-[400px] flex flex-col shadow-sm border border-surface-container-low/50">
          <div className="flex justify-between items-center mb-8">
            <h3 className="text-lg font-bold headline">Activity Projection</h3>
            <div className="flex gap-4">
              <div className="flex items-center gap-2">
                <span className="w-3 h-3 rounded-full bg-primary"></span>
                <span className="text-xs font-medium text-on-surface-variant">Posts</span>
              </div>
              <div className="flex items-center gap-2">
                <span className="w-3 h-3 rounded-full bg-tertiary"></span>
                <span className="text-xs font-medium text-on-surface-variant">Votes</span>
              </div>
            </div>
          </div>
          <div className="flex-1 relative flex items-end gap-2 px-2">
            {/* Simulated Chart Visuals */}
            <div className="flex-1 bg-surface-container-low h-32 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-12 rounded-t-lg transition-all group-hover:h-16"></div>
            </div>
            <div className="flex-1 bg-surface-container-low h-48 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-24 rounded-t-lg transition-all group-hover:h-32"></div>
            </div>
            <div className="flex-1 bg-surface-container-low h-64 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-40 rounded-t-lg transition-all group-hover:h-48"></div>
            </div>
            <div className="flex-1 bg-surface-container-low h-40 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-20 rounded-t-lg transition-all group-hover:h-24"></div>
            </div>
            <div className="flex-1 bg-surface-container-low h-72 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-56 rounded-t-lg transition-all group-hover:h-64"></div>
            </div>
            <div className="flex-1 bg-surface-container-low h-52 rounded-t-lg relative group transition-all hover:bg-primary-container/40">
              <div className="absolute bottom-0 w-full bg-primary h-32 rounded-t-lg transition-all group-hover:h-40"></div>
            </div>
          </div>
          <div className="flex justify-between mt-4 px-2 text-[10px] font-bold text-on-surface-variant uppercase tracking-widest font-label">
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
          </div>
        </div>

        {/* Side Panels (Asymmetric Column) */}
        <div className="space-y-6">
          {/* Reported Content Preview */}
          <div className="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-surface-container-low/50">
            <div className="flex justify-between items-center mb-6">
              <h3 className="text-sm font-bold uppercase tracking-widest font-label">Report Queue</h3>
              <a className="text-primary text-xs font-bold hover:underline" href="#">View All</a>
            </div>
            <div className="space-y-4">
              <div className="p-4 bg-surface-container-low rounded-xl flex gap-3 items-start">
                <span className="material-symbols-outlined text-error text-lg">warning</span>
                <div>
                  <p className="text-xs font-bold leading-tight">Doxxing Attempt in /r/politics</p>
                  <p className="text-[10px] text-on-surface-variant mt-1">Reported 2m ago by anon_77</p>
                </div>
              </div>
              <div className="p-4 bg-surface-container-low rounded-xl flex gap-3 items-start">
                <span className="material-symbols-outlined text-secondary text-lg">error_outline</span>
                <div>
                  <p className="text-xs font-bold leading-tight">Spam Bot - Crypto Links</p>
                  <p className="text-[10px] text-on-surface-variant mt-1">Reported 14m ago by System</p>
                </div>
              </div>
              <div className="p-4 bg-surface-container-low rounded-xl flex gap-3 items-start opacity-60">
                <span className="material-symbols-outlined text-on-surface-variant text-lg">check_circle</span>
                <div>
                  <p className="text-xs font-bold leading-tight line-through">Offensive Avatar</p>
                  <p className="text-[10px] text-on-surface-variant mt-1">Dismissed by Mod_Luna</p>
                </div>
              </div>
            </div>
          </div>

          {/* Category Management */}
          <div className="bg-primary-gradient p-6 rounded-xl text-white shadow-xl shadow-primary/30">
            <h3 className="text-sm font-bold mb-4 flex items-center gap-2">
              <span className="material-symbols-outlined">category</span>
              Top Categories
            </h3>
            <div className="space-y-3">
              <div className="flex justify-between items-center bg-white/10 p-3 rounded-lg backdrop-blur-sm">
                <span className="text-xs font-medium">/r/deep-state</span>
                <span className="text-xs font-bold">12.4k</span>
              </div>
              <div className="flex justify-between items-center bg-white/10 p-3 rounded-lg backdrop-blur-sm">
                <span className="text-xs font-medium">/r/tech-leaks</span>
                <span className="text-xs font-bold">8.1k</span>
              </div>
              <div className="flex justify-between items-center bg-white/10 p-3 rounded-lg backdrop-blur-sm">
                <span className="text-xs font-medium">/r/whistleblower</span>
                <span className="text-xs font-bold">5.3k</span>
              </div>
            </div>
            <button className="w-full mt-4 py-2 text-xs font-bold bg-white text-primary rounded-lg hover:bg-white/90 transition-colors">
              Manage All
            </button>
          </div>
        </div>
      </section>

      {/* Bottom Row: Statistics & Mod Performance */}
      <section className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {/* Word Filters Card */}
        <div className="bg-surface-container-lowest p-6 rounded-xl flex flex-col shadow-sm border border-surface-container-low/50">
          <h3 className="text-sm font-bold uppercase tracking-widest mb-4 font-label">Live Filter Hits</h3>
          <div className="flex-1 space-y-4">
            <div className="flex items-center justify-between">
              <span className="text-xs font-semibold px-2 py-1 bg-surface-container text-on-surface rounded">Toxicity</span>
              <div className="flex-1 mx-4 h-1 bg-surface-container rounded-full overflow-hidden">
                <div className="bg-error h-full" style={{ width: "65%" }}></div>
              </div>
              <span className="text-[10px] font-bold">65%</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-semibold px-2 py-1 bg-surface-container text-on-surface rounded">Spam</span>
              <div className="flex-1 mx-4 h-1 bg-surface-container rounded-full overflow-hidden">
                <div className="bg-primary h-full" style={{ width: "42%" }}></div>
              </div>
              <span className="text-[10px] font-bold">42%</span>
            </div>
            <div className="flex items-center justify-between">
              <span className="text-xs font-semibold px-2 py-1 bg-surface-container text-on-surface rounded">Doxxing</span>
              <div className="flex-1 mx-4 h-1 bg-surface-container rounded-full overflow-hidden">
                <div className="bg-amber-500 h-full" style={{ width: "12%" }}></div>
              </div>
              <span className="text-[10px] font-bold">12%</span>
            </div>
          </div>
        </div>

        {/* Admin Action History */}
        <div className="bg-surface-container-lowest p-6 rounded-xl shadow-sm border border-surface-container-low/50">
          <h3 className="text-sm font-bold uppercase tracking-widest mb-4 font-label">Audit Snippet</h3>
          <div className="space-y-4">
            <div className="flex gap-4">
              <div className="flex-shrink-0 w-8 h-8 rounded bg-slate-100 flex items-center justify-center">
                <span className="material-symbols-outlined text-sm">block</span>
              </div>
              <div>
                <p className="text-xs font-bold">User anon_492 banned</p>
                <p className="text-[10px] text-on-surface-variant">By Auditor_12 • 5m ago</p>
              </div>
            </div>
            <div className="flex gap-4">
              <div className="flex-shrink-0 w-8 h-8 rounded bg-slate-100 flex items-center justify-center">
                <span className="material-symbols-outlined text-sm">history</span>
              </div>
              <div>
                <p className="text-xs font-bold">Filter "Election" updated</p>
                <p className="text-[10px] text-on-surface-variant">By System • 1h ago</p>
              </div>
            </div>
          </div>
        </div>

        {/* Mod Requests */}
        <div className="bg-surface-container-high p-6 rounded-xl border border-surface-container-low/50">
          <div className="flex items-center justify-between mb-4">
            <h3 className="text-sm font-bold">Mod Applications</h3>
            <span className="bg-primary text-white text-[10px] font-bold px-2 py-1 rounded-full">3 New</span>
          </div>
          <div className="relative overflow-hidden group p-4 rounded-xl bg-surface-container-lowest transition-transform hover:-translate-y-1 shadow-sm">
            <div className="flex items-center gap-3 mb-2">
              <div className="w-8 h-8 rounded-full bg-tertiary-container flex items-center justify-center text-on-tertiary-container font-bold text-xs">A</div>
              <p className="text-xs font-bold">Anon_889</p>
            </div>
            <p className="text-[11px] text-on-surface-variant line-clamp-2 italic">"I have 5 years experience moderating decentralized communities and understanding..."</p>
            <div className="mt-4 flex gap-2">
              <button className="flex-1 py-1.5 bg-primary text-white text-[10px] font-bold rounded-lg hover:opacity-90">Review</button>
              <button className="flex-1 py-1.5 bg-surface-container text-on-surface text-[10px] font-bold rounded-lg hover:bg-surface-variant">Deny</button>
            </div>
          </div>
        </div>
      </section>

      {/* FAB for quick Admin Actions */}
      <button className="fixed bottom-8 right-8 w-16 h-16 bg-primary-gradient text-white rounded-full flex items-center justify-center shadow-2xl shadow-primary/40 hover:scale-105 active:scale-95 transition-all z-50">
        <span className="material-symbols-outlined text-3xl">add</span>
      </button>
    </div>
  );
}
