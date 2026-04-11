export default function CreatePostPage() {
  return (
    <div className="max-w-5xl mx-auto flex flex-col md:flex-row gap-12">
      {/* Form Section: Left Column */}
      <div className="flex-1">
        <header className="mb-10">
          <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-3 headline">Initialize Entry</h1>
          <p className="text-on-surface-variant font-body text-lg max-w-xl leading-relaxed">
            Securely broadcast your findings to the ledger. Your identity remains protected by Sovereign architecture protocols.
          </p>
        </header>
        <form className="space-y-10">
          {/* Title & Description */}
          <div className="space-y-8">
            <div className="group">
              <label className="flex items-center gap-2 text-sm font-label font-bold tracking-wider text-on-surface-variant mb-3 uppercase" htmlFor="headline">
                <span className="material-symbols-outlined text-sm">title</span> Subject Headline
              </label>
              <input
                className="w-full bg-surface-container-low border-2 border-surface-variant/30 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all px-4 py-4 text-xl font-headline font-bold text-on-surface placeholder:text-outline-variant/60 outline-none"
                id="headline"
                placeholder="e.g., Critical Vulnerability in Node-402 Settlement Logic"
                type="text"
              />
            </div>

            <div className="group">
              <label className="flex items-center gap-2 text-sm font-label font-bold tracking-wider text-on-surface-variant mb-3 uppercase" htmlFor="problem">
                <span className="material-symbols-outlined text-sm">description</span> Problem Description
              </label>
              <textarea
                className="w-full bg-surface-container-low border-2 border-surface-variant/30 rounded-xl focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all p-4 text-on-surface leading-relaxed placeholder:text-outline-variant/60 outline-none"
                id="problem"
                placeholder="Provide a detailed technical breakdown of the anomaly. Include logs, timestamps, and observed impact..."
                rows={8}
              ></textarea>
            </div>
          </div>

          {/* Category Selection */}
          <div>
            <label className="flex items-center gap-2 text-sm font-label font-bold tracking-wider text-on-surface-variant mb-4 uppercase">
              <span className="material-symbols-outlined text-sm">label</span> Taxonomy Categories
            </label>
            <div className="flex flex-wrap gap-2 mb-4 min-h-[44px] p-2 bg-surface-container-low rounded-xl border-2 border-surface-variant/30 border-dashed">
              <span className="px-4 py-1.5 bg-primary-container text-on-primary-container rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
                Governance <button className="material-symbols-outlined text-base hover:bg-on-primary-container/10 rounded-full p-0.5" type="button">close</button>
              </span>
              <span className="px-4 py-1.5 bg-primary-container text-on-primary-container rounded-lg text-sm font-semibold flex items-center gap-2 shadow-sm">
                Audit Trace <button className="material-symbols-outlined text-base hover:bg-on-primary-container/10 rounded-full p-0.5" type="button">close</button>
              </span>
              <button className="px-4 py-1.5 border-2 border-primary/20 text-primary rounded-lg text-sm font-semibold flex items-center gap-2 hover:bg-primary/5 transition-colors" type="button">
                <span className="material-symbols-outlined text-sm">add</span> Add New
              </button>
            </div>
          </div>

          {/* Image Upload Zone */}
          <div>
            <label className="flex items-center gap-2 text-sm font-label font-bold tracking-wider text-on-surface-variant mb-4 uppercase">
              <span className="material-symbols-outlined text-sm">attachment</span> Visual Evidence (Optional)
            </label>
            <div className="border-2 border-dashed border-surface-variant/50 rounded-2xl p-10 text-center bg-surface-container-low/30 hover:border-primary/50 hover:bg-surface-container-low transition-all cursor-pointer group">
              <div className="flex flex-col items-center gap-4">
                <div className="h-16 w-16 rounded-2xl bg-primary/10 text-primary flex items-center justify-center group-hover:scale-110 group-hover:bg-primary group-hover:text-white transition-all duration-300">
                  <span className="material-symbols-outlined text-3xl">upload_file</span>
                </div>
                <div>
                  <p className="text-on-surface font-bold text-lg">Drop assets or click to browse</p>
                  <div className="mt-2 flex items-center justify-center gap-4">
                    <span className="flex items-center gap-1 text-xs font-semibold text-on-surface-variant bg-surface-variant/40 px-2 py-1 rounded-md">
                      <span className="material-symbols-outlined text-sm">image</span> PNG, JPG, RAW
                    </span>
                    <span className="flex items-center gap-1 text-xs font-semibold text-on-surface-variant bg-surface-variant/40 px-2 py-1 rounded-md">
                      <span className="material-symbols-outlined text-sm">database</span> MAX 25MB
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>

          {/* Anonymity Toggle */}
          <div className="p-1 bg-gradient-to-r from-primary/20 to-tertiary/20 rounded-2xl">
            <div className="flex items-center justify-between p-6 bg-surface-container-lowest rounded-[14px]">
              <div className="flex items-center gap-5">
                <div className="p-4 bg-primary text-white rounded-xl shadow-lg shadow-primary/20">
                  <span className="material-symbols-outlined text-2xl">fingerprint</span>
                </div>
                <div>
                  <p className="text-lg font-extrabold text-on-surface headline">Confirm Anonymity</p>
                  <p className="text-sm text-on-surface-variant max-w-sm font-body">Your user ID will be cryptographically hashed and replaced with a temporary Cipher Alias.</p>
                </div>
              </div>
              <label className="relative inline-flex items-center cursor-pointer group">
                <input className="sr-only peer" type="checkbox" defaultChecked />
                <div className="w-16 h-9 bg-surface-variant peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:left-[4px] after:content-[''] after:absolute after:top-[4px] after:left-[4px] after:bg-white after:rounded-full after:h-7 after:w-7 after:transition-all after:shadow-sm peer-checked:bg-primary group-hover:ring-4 group-hover:ring-primary/10"></div>
              </label>
            </div>
          </div>

          {/* Actions */}
          <div className="flex flex-col sm:flex-row gap-4 pt-6">
            <button className="flex-1 bg-primary-gradient text-white font-headline font-extrabold py-5 rounded-2xl text-xl shadow-xl shadow-primary/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-3" type="submit">
              <span className="material-symbols-outlined">send</span> Publish Entry
            </button>
            <button className="px-10 py-5 bg-surface-container-highest text-on-surface font-extrabold rounded-2xl hover:bg-surface-variant transition-all hover:shadow-lg active:scale-[0.98]" type="button">
              Save Draft
            </button>
          </div>
        </form>
      </div>

      {/* Sidebar / Guidance: Right Column */}
      <aside className="md:w-80 space-y-8">
        <div className="p-8 bg-surface-container-low rounded-2xl border border-surface-variant/30">
          <h3 className="font-headline font-bold text-xl mb-6 flex items-center gap-3">
            <span className="material-symbols-outlined text-primary p-2 bg-primary/10 rounded-lg">shield</span>
            Ledger Protocols
          </h3>
          <ul className="space-y-5 text-sm text-on-surface-variant leading-relaxed font-body">
            <li className="flex gap-4">
              <span className="flex-none w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">01</span>
              <span>Avoid direct mentions of proprietary identities or personal names.</span>
            </li>
            <li className="flex gap-4">
              <span className="flex-none w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">02</span>
              <span>Ensure all visual evidence is scrubbed of metadata before upload.</span>
            </li>
            <li className="flex gap-4">
              <span className="flex-none w-6 h-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">03</span>
              <span>Moderation logs will reflect any violations of the Sovereign Privacy Act.</span>
            </li>
          </ul>
        </div>

        {/* Contextual Card */}
        <div className="relative h-72 rounded-2xl overflow-hidden group shadow-2xl shadow-primary/10">
          <img alt="Security concept" className="absolute inset-0 object-cover w-full h-full brightness-50 group-hover:scale-110 transition-transform duration-1000" src="https://lh3.googleusercontent.com/aida-public/AB6AXuC7DQbZly1DxhvtUa6AXD_OFCD2x861vjYk32q5cOimT06bG7MElvrffJr4_b7OYHth7A_cWb6hq01uDO0HDHnyMlrJOt5AuB-slGyDkYK3FpmRS7yeW8I9eA9CxBZA-HH1aX0LdZuT5-_z7ipIwatw06JGIqLa8ouu5uWqBpm2bHThzJPgW1YKo6WLEiDZtG05HBlh0wHFHA2HU4eklccatNXY1YSJPYZCcBUlYVysnUmifFnPh61cZ2MgAZrOHXvWFqipxO5GligY" />
          <div className="absolute inset-0 p-8 flex flex-col justify-end bg-gradient-to-t from-black/80 via-transparent to-transparent">
            <p className="text-primary font-bold text-xs tracking-widest uppercase mb-2">Architecture</p>
            <h4 className="text-white font-headline font-bold text-xl leading-tight">Quantum-Leap Encryption</h4>
            <p className="text-white/70 text-sm mt-3 font-body">Every post is fragmented across 4,096 immutable nodes.</p>
          </div>
        </div>
      </aside>
    </div>
  );
}
