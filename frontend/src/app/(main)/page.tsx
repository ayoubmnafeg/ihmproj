export default function HomePage() {
  return (
    <>
      {/* Main Content Area */}
      <main className="flex-1 p-8">
        <header className="max-w-4xl mx-auto mb-12">
          <h1 className="text-5xl font-extrabold tracking-tight text-on-surface mb-4 headline">Architectural Whisper</h1>
          <p className="text-lg text-on-surface-variant max-w-2xl leading-relaxed font-body">
            Secure discourse through authoritative minimalism. Your identity is protected by the structure of the interface.
          </p>
        </header>

        {/* Feed Grid */}
        <div className="max-w-4xl mx-auto flex flex-col gap-8">
          {/* Post Card 1 */}
          <article className="bg-surface-container-lowest rounded-xl p-6 transition-all hover:translate-y-[-2px] group shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center">
                <span className="material-symbols-outlined text-slate-400">person_off</span>
              </div>
              <div>
                <p className="text-sm font-bold text-on-surface">Anonymous Resident</p>
                <p className="text-xs text-on-surface-variant">Posted in <span className="text-primary font-medium">#UrbanPlanning</span> • 2h ago</p>
              </div>
            </div>
            <h2 className="text-2xl font-bold mb-3 group-hover:text-primary transition-colors headline">The paradox of density in modern high-rises</h2>
            <p className="text-on-surface-variant leading-relaxed mb-6 font-body">
              We often talk about vertical living as a solution to urban sprawl, but we rarely discuss the social isolation it mandates. When we don't share hallways or porches, the architectural design creates a digital-only social fabric. How do we reclaim communal space without sacrificing the anonymity we cherish in the city?
            </p>
            <div className="flex items-center justify-between pt-6 border-t border-surface-container">
              <div className="flex items-center gap-6">
                <div className="flex items-center gap-1">
                  <button className="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-2 rounded-full hover:bg-primary-container/20">thumb_up</button>
                  <span className="text-sm font-bold text-on-surface-variant">1.2k</span>
                  <button className="material-symbols-outlined text-on-surface-variant hover:text-error transition-colors p-2 rounded-full hover:bg-error-container/20">thumb_down</button>
                </div>
                <button className="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors p-2 rounded-full hover:bg-primary-container/20">
                  <span className="material-symbols-outlined">mode_comment</span>
                  <span className="text-sm font-bold">142</span>
                </button>
              </div>
              <div className="flex items-center gap-2">
                <button className="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-2 text-[20px]">bookmark</button>
                <button className="material-symbols-outlined text-on-surface-variant hover:text-error transition-colors p-2 text-[20px]">report</button>
              </div>
            </div>
          </article>

          {/* Post Card 2 */}
           <article className="bg-surface-container-lowest rounded-xl p-6 transition-all hover:translate-y-[-2px] group shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
            <div className="flex items-center gap-3 mb-4">
              <div className="w-10 h-10 rounded-full bg-surface-container-high flex items-center justify-center">
                <span className="material-symbols-outlined text-slate-400">person_off</span>
              </div>
              <div>
                <p className="text-sm font-bold text-on-surface">Digital Ghost</p>
                <p className="text-xs text-on-surface-variant">Posted in <span className="text-primary font-medium">#CyberSecurity</span> • 4h ago</p>
              </div>
            </div>
            <div className="flex flex-col md:flex-row gap-6 mb-6">
              <div className="flex-1">
                <h2 className="text-2xl font-bold mb-3 group-hover:text-primary transition-colors headline">Quantum resistance and the ledger</h2>
                <p className="text-on-surface-variant leading-relaxed mb-4 font-body">
                  As we move closer to viable quantum computing, the standard encryption protocols we rely on for anonymity will become the very chains that expose us. We need to start pivoting to lattice-based cryptography before the harvest-now-decrypt-later attacks become permanent.
                </p>
                <div className="flex flex-wrap gap-2">
                  <span className="px-3 py-1 bg-surface-container text-on-surface-variant text-xs font-bold rounded-full font-label">CRYPTO</span>
                  <span className="px-3 py-1 bg-surface-container text-on-surface-variant text-xs font-bold rounded-full font-label">PRIVACY</span>
                </div>
              </div>
              <div className="md:w-1/3">
                <div className="w-full h-48 rounded-xl overflow-hidden bg-surface-container">
                  <img alt="abstract representation of digital encryption" className="w-full h-full object-cover opacity-90 hover:scale-105 transition-transform duration-500" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAeVBd2A-LzJYnV2YHYhKEWeFtQZ_uoLVfw-ndlNBTOX89LXjqeEzdETiYBq1vTJhus4H6VNRjvUMQwxt8H_6gbfcs3bbN6HtyFbMs5yTkgDb_PJwCDCRbOEZstnSTnA9xX3IEUefzDT1OuJ1zn2eq59cYuW6eLA5F5OdkCSq3i_g3beWZk8_eJgIq4pSnC0ImdrkN_0NdEyKoLnBPlLufRxaBMkM55-Vh_hddNvnUy1I6SegzSjOzosAooMesku6VI-lQK_UmfJJ-y" />
                </div>
              </div>
            </div>
            <div className="flex items-center justify-between pt-6 border-t border-surface-container">
              <div className="flex items-center gap-6">
                <div className="flex items-center gap-1">
                  <button className="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-2 rounded-full hover:bg-primary-container/20">thumb_up</button>
                  <span className="text-sm font-bold text-on-surface-variant">856</span>
                  <button className="material-symbols-outlined text-on-surface-variant hover:text-error transition-colors p-2 rounded-full hover:bg-error-container/20">thumb_down</button>
                </div>
                <button className="flex items-center gap-2 text-on-surface-variant hover:text-primary transition-colors p-2 rounded-full hover:bg-primary-container/20">
                  <span className="material-symbols-outlined">mode_comment</span>
                  <span className="text-sm font-bold">56</span>
                </button>
              </div>
              <div className="flex items-center gap-2">
                <button className="material-symbols-outlined text-on-surface-variant hover:text-primary transition-colors p-2 text-[20px]">bookmark</button>
                <button className="material-symbols-outlined text-on-surface-variant hover:text-error transition-colors p-2 text-[20px]">report</button>
              </div>
            </div>
          </article>
        </div>
      </main>

      {/* Right Sidebar (Trends/Stats) */}
      <aside className="hidden xl:block w-80 p-8 sticky top-16 h-[calc(100vh-4rem)] overflow-y-auto">
        <div className="bg-surface-container-low rounded-xl p-6 mb-6">
          <h3 className="text-sm font-black text-slate-900 uppercase tracking-widest mb-4">Trending Topics</h3>
          <ul className="space-y-4">
            <li>
              <a className="group block" href="#">
                <p className="text-xs text-on-surface-variant mb-1">#GlobalPolitics</p>
                <p className="text-sm font-bold text-on-surface group-hover:text-primary transition-colors">The shifting sands of the Arctic council</p>
                <p className="text-xs text-on-surface-variant">2.4k active participants</p>
              </a>
            </li>
            <li>
              <a className="group block" href="#">
                <p className="text-xs text-on-surface-variant mb-1">#MedTech</p>
                <p className="text-sm font-bold text-on-surface group-hover:text-primary transition-colors">Longevity protocols: Fact vs Fiction</p>
                <p className="text-xs text-on-surface-variant">1.8k active participants</p>
              </a>
            </li>
            <li>
              <a className="group block" href="#">
                <p className="text-xs text-on-surface-variant mb-1">#DeepWeb</p>
                <p className="text-sm font-bold text-on-surface group-hover:text-primary transition-colors">New onion protocols in testing</p>
                <p className="text-xs text-on-surface-variant">900 active participants</p>
              </a>
            </li>
          </ul>
        </div>
        
        <div className="bg-primary/5 rounded-xl p-6 border border-primary/10">
          <h3 className="text-sm font-black text-primary uppercase tracking-widest mb-2">Governance Alert</h3>
          <p className="text-sm text-on-surface-variant leading-snug mb-4">A new protocol amendment has been proposed for the reputation weighting system. Review it in the Admin Panel.</p>
          <button className="w-full py-2 bg-primary text-white text-xs font-bold rounded-xl hover:opacity-90 transition-opacity">Review Proposals</button>
        </div>
      </aside>
    </>
  );
}
