import { PublicationEditor } from "@/components/publications/publication-editor";
import { RequireAuth } from "@/components/publications/require-auth";

export default function CreatePostPage() {
  return (
    <div className="max-w-5xl mx-auto p-8">
      <RequireAuth nextPath="/create">
        <header className="mb-10">
          <h1 className="text-4xl md:text-5xl font-extrabold tracking-tight text-on-surface mb-3 headline">
            Create a Publication
          </h1>
          <p className="text-on-surface-variant font-body text-lg max-w-xl leading-relaxed">
            Publish a new entry through the Laravel publication API.
          </p>
        </header>
        <PublicationEditor mode="create" />
      </RequireAuth>
    </div>
  );
}
