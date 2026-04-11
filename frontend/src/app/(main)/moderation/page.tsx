export default function ModerationPage() {
  return (
    <main className="flex-1 w-full p-8 max-w-7xl mx-auto">
      {/* Header Section */}
      <header className="flex justify-between items-end mb-10">
        <div>
          <h2 className="text-4xl font-extrabold text-on-surface tracking-tight font-headline">Review Queue</h2>
          <p className="text-on-surface-variant mt-2 font-medium">Processing pending reports and community flags.</p>
        </div>
        <div className="flex gap-4">
          <div className="flex items-center gap-2 bg-surface-container-low px-4 py-2 rounded-xl border border-surface-container-low/50">
            <span className="text-xs font-bold text-on-surface-variant uppercase tracking-widest font-label">Efficiency Rating</span>
            <span className="text-primary font-bold">98.4%</span>
          </div>
        </div>
      </header>

      {/* Stats & Filter Bar */}
      <div className="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div className="bg-surface-container-lowest p-6 rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
          <p className="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1 font-label">Total Pending</p>
          <p className="text-3xl font-extrabold font-headline">142</p>
        </div>
        <div className="bg-surface-container-lowest p-6 rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
          <p className="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1 font-label">High Severity</p>
          <p className="text-3xl font-extrabold font-headline text-error">12</p>
        </div>
        <div className="bg-surface-container-lowest p-6 rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
          <p className="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1 font-label">Avg Response</p>
          <p className="text-3xl font-extrabold font-headline">14m</p>
        </div>
        <div className="bg-surface-container-lowest p-6 rounded-xl shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50 flex items-center justify-between">
          <div>
            <p className="text-xs font-bold text-on-surface-variant uppercase tracking-widest mb-1 font-label">Shift Timer</p>
            <p className="text-3xl font-extrabold font-headline">04:22:10</p>
          </div>
          <span className="material-symbols-outlined text-primary text-3xl opacity-20">timer</span>
        </div>
      </div>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Sidebar Filters */}
        <aside className="w-full lg:w-72 flex flex-col gap-6">
          <div className="bg-surface-container-low p-6 rounded-xl border border-surface-container-low/50">
            <h3 className="text-sm font-bold text-on-surface mb-4 uppercase tracking-widest font-label">Filter Reports</h3>
            <div className="space-y-4">
              <div>
                <label className="block text-xs font-semibold text-on-surface-variant mb-2">Category</label>
                <select className="w-full bg-surface-container-lowest border-0 rounded-lg text-sm focus:ring-2 focus:ring-primary shadow-sm outline-none px-3 py-2">
                  <option>All Categories</option>
                  <option>Hate Speech</option>
                  <option>Harassment</option>
                  <option>Spam</option>
                  <option>Misinformation</option>
                </select>
              </div>
              <div>
                <label className="block text-xs font-semibold text-on-surface-variant mb-2">Severity</label>
                <div className="flex flex-col gap-2">
                  <label className="flex items-center gap-2 text-sm cursor-pointer group">
                    <input defaultChecked className="rounded text-primary focus:ring-primary bg-surface-container-lowest border-0 shadow-sm" type="checkbox"/>
                    <span className="text-on-surface-variant group-hover:text-on-surface">Critical</span>
                  </label>
                  <label className="flex items-center gap-2 text-sm cursor-pointer group">
                    <input defaultChecked className="rounded text-primary focus:ring-primary bg-surface-container-lowest border-0 shadow-sm" type="checkbox"/>
                    <span className="text-on-surface-variant group-hover:text-on-surface">Moderate</span>
                  </label>
                  <label className="flex items-center gap-2 text-sm cursor-pointer group">
                    <input className="rounded text-primary focus:ring-primary bg-surface-container-lowest border-0 shadow-sm" type="checkbox"/>
                    <span className="text-on-surface-variant group-hover:text-on-surface">Low</span>
                  </label>
                </div>
              </div>
              <button className="w-full bg-surface-container-highest text-on-surface-variant py-2 rounded-lg text-sm font-bold mt-4 hover:bg-surface-variant transition-colors">
                Clear Filters
              </button>
            </div>
          </div>
          
          <div className="bg-surface-container-low p-6 rounded-xl border border-surface-container-low/50">
            <h3 className="text-sm font-bold text-on-surface mb-4 uppercase tracking-widest font-label">Recent Activity</h3>
            <div className="space-y-4">
              <div className="flex items-start gap-3">
                <div className="w-2 h-2 rounded-full bg-primary mt-1.5"></div>
                <div>
                  <p className="text-xs font-semibold">User #8291 Banned</p>
                  <p className="text-[10px] text-on-surface-variant">2 minutes ago by You</p>
                </div>
              </div>
              <div className="flex items-start gap-3">
                <div className="w-2 h-2 rounded-full bg-tertiary mt-1.5"></div>
                <div>
                  <p className="text-xs font-semibold">Post #AF02 Approved</p>
                  <p className="text-[10px] text-on-surface-variant">15 minutes ago by You</p>
                </div>
              </div>
            </div>
          </div>
        </aside>

        {/* Report Feed */}
        <section className="flex-1 space-y-6">
          {/* Report Card 1 */}
          <div className="bg-surface-container-lowest rounded-xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50 group hover:shadow-md transition-shadow">
            <div className="flex justify-between items-start mb-4">
              <div className="flex items-center gap-3">
                <div className="bg-error/10 text-error px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter">High Severity</div>
                <span className="text-xs font-bold text-on-surface-variant">Report ID: #MOD-7721</span>
              </div>
              <div className="text-xs text-on-surface-variant italic">Reported 4m ago</div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
              <div className="md:col-span-8">
                <h4 className="text-lg font-bold mb-2 font-headline">"The financial system is a house of cards, we must dismantle it by force tonight at..."</h4>
                <p className="text-on-surface-variant text-sm leading-relaxed mb-4 font-body">
                  This user is repeatedly calling for physical disruption of infrastructure in the #Economics thread. Multiple flags for 'Incitement of Violence'.
                </p>
                <div className="flex items-center gap-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant font-label">
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">person</span> Anon #3102</span>
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">flag</span> 14 Reports</span>
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">warning</span> 2 Past Warns</span>
                </div>
              </div>
              <div className="md:col-span-4 flex flex-col gap-2 justify-center">
                <button className="w-full bg-gradient-to-br from-primary to-primary-dim text-white py-2.5 rounded-xl text-sm font-bold hover:opacity-90 transition-all flex items-center justify-center gap-2">
                  <span className="material-symbols-outlined text-sm">check_circle</span> Approve
                </button>
                <div className="grid grid-cols-2 gap-2">
                  <button className="bg-surface-container-high text-on-surface-variant py-2 rounded-xl text-xs font-bold hover:bg-error/10 hover:text-error transition-all">
                    Delete
                  </button>
                  <button className="bg-surface-container-high text-on-surface-variant py-2 rounded-xl text-xs font-bold hover:bg-tertiary-container hover:text-on-tertiary-container transition-all">
                    Warn
                  </button>
                </div>
                <button className="w-full bg-error text-white py-2 rounded-xl text-xs font-bold hover:bg-error-dim transition-all">
                  Block User
                </button>
              </div>
            </div>
          </div>

          {/* Report Card 2 (Nesting Example) */}
          <div className="bg-surface-container-lowest rounded-xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50 group">
            <div className="flex justify-between items-start mb-4">
              <div className="flex items-center gap-3">
                <div className="bg-tertiary-container text-on-tertiary-container px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter text-blue-700">Moderate</div>
                <span className="text-xs font-bold text-on-surface-variant">Report ID: #MOD-7718</span>
              </div>
              <div className="text-xs text-on-surface-variant italic">Reported 12m ago</div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
              <div className="md:col-span-8">
                <div className="bg-surface-container-low p-4 rounded-xl mb-4 border-l-4 border-primary/20">
                  <p className="text-xs font-bold text-primary mb-1 uppercase tracking-widest font-label">Original Post Context</p>
                  <p className="text-sm font-medium italic font-body">"Does anyone have reliable data on the new tax protocol?"</p>
                </div>
                <h4 className="text-md font-bold mb-2 font-headline">Reported Comment: "You're a complete idiot for even asking that. Go back to your hole, nobody wants you here."</h4>
                <p className="text-on-surface-variant text-sm mb-4 font-body">Reason: Targeted Harassment / Rule 1 (Civility)</p>
                <div className="flex items-center gap-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant font-label">
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">person</span> Anon #9921</span>
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">flag</span> 2 Reports</span>
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">history</span> New User</span>
                </div>
              </div>
              <div className="md:col-span-4 flex flex-col gap-2 justify-center">
                <button className="w-full bg-gradient-to-br from-primary to-primary-dim text-white py-2.5 rounded-xl text-sm font-bold hover:opacity-90 transition-all">
                  Approve
                </button>
                <div className="grid grid-cols-2 gap-2">
                  <button className="bg-surface-container-high text-on-surface-variant py-2 rounded-xl text-xs font-bold hover:bg-error/10 hover:text-error transition-all">
                    Delete
                  </button>
                  <button className="bg-surface-container-high text-on-surface-variant py-2 rounded-xl text-xs font-bold hover:bg-tertiary-container hover:text-on-tertiary-container transition-all">
                    Warn
                  </button>
                </div>
                <button className="w-full bg-error text-white py-2 rounded-xl text-xs font-bold hover:bg-error-dim transition-all">
                  Block User
                </button>
              </div>
            </div>
          </div>

          {/* Report Card 3 */}
          <div className="bg-surface-container-lowest rounded-xl p-6 shadow-sm border border-surface-container-low/50 group opacity-75 grayscale hover:opacity-100 hover:grayscale-0 transition-all">
            <div className="flex justify-between items-start mb-4">
              <div className="flex items-center gap-3">
                <div className="bg-surface-variant text-on-surface-variant px-2 py-1 rounded-md text-[10px] font-black uppercase tracking-tighter">Low Priority</div>
                <span className="text-xs font-bold text-on-surface-variant">Report ID: #MOD-7715</span>
              </div>
              <div className="text-xs text-on-surface-variant italic">Reported 28m ago</div>
            </div>
            <div className="grid grid-cols-1 md:grid-cols-12 gap-6">
              <div className="md:col-span-8">
                <h4 className="text-md font-bold mb-2 font-headline">"FREE TOKENS!!! CLICK LINK NOW TO CLAIM YOUR AIRDROP!!! [REDACTED LINK]"</h4>
                <p className="text-on-surface-variant text-sm mb-4 font-body">Reason: Spam / Phishing Link</p>
                <div className="flex items-center gap-4 text-xs font-bold uppercase tracking-wider text-on-surface-variant font-label">
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">person</span> Anon #1128</span>
                  <span className="flex items-center gap-1"><span className="material-symbols-outlined text-sm">flag</span> 45 Reports</span>
                </div>
              </div>
              <div className="md:col-span-4 flex flex-col gap-2 justify-center">
                <button className="w-full bg-gradient-to-br from-primary to-primary-dim text-white py-2.5 rounded-xl text-sm font-bold">
                  Approve
                </button>
                <div className="grid grid-cols-2 gap-2">
                  <button className="bg-error text-white py-2 rounded-xl text-xs font-bold">
                    Quick Delete
                  </button>
                  <button className="bg-error text-white py-2 rounded-xl text-xs font-bold">
                    Quick Ban
                  </button>
                </div>
              </div>
            </div>
          </div>

          <div className="mt-12 flex justify-center">
            <button className="px-8 py-3 bg-surface-container-lowest border border-surface-container-low/50 rounded-xl font-bold text-on-surface-variant shadow-sm hover:translate-y-[-2px] transition-transform flex items-center gap-3">
              <span className="material-symbols-outlined">expand_more</span>
              Show Older Reports
            </button>
          </div>
        </section>
      </div>

    </main>
  );
}
