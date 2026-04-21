import { PublicationFeed } from "@/components/publications/publication-feed";

export default function HomePage() {
  return (
    <>
      <main className="flex-1 p-8">
        <header className="max-w-4xl mx-auto mb-12">
          <h1 className="text-5xl font-extrabold tracking-tight text-on-surface mb-4 headline">
            Public Feed
          </h1>
          <p className="text-lg text-on-surface-variant max-w-2xl leading-relaxed font-body">
            Guests can read visible publications. Authenticated users can
            publish and manage their own posts.
          </p>
        </header>
        <PublicationFeed />
      </main>
    </>
  );
}
