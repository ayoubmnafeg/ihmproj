"use client";

import { useEffect, useState } from "react";
import { useParams, useRouter } from "next/navigation";
import { useAuth } from "@/components/auth/use-auth";
import { createApiClient } from "@/lib/api";

export function PublicationEditor({ mode }: { mode: "create" | "edit" }) {
  const router = useRouter();
  const params = useParams<{ publicationId: string }>();
  const { token } = useAuth();
  const [title, setTitle] = useState("");
  const [text, setText] = useState("");
  const [error, setError] = useState<string | null>(null);
  const [pending, setPending] = useState(false);
  const [loading, setLoading] = useState(mode === "edit");

  useEffect(() => {
    if (mode !== "edit") {
      return;
    }

    const api = createApiClient();

    api.getPublication(params.publicationId)
      .then((publication) => {
        setTitle(publication.title);
        setText(publication.text);
        setLoading(false);
      })
      .catch((caught) => {
        setError(
          caught instanceof Error ? caught.message : "Failed to load publication.",
        );
        setLoading(false);
      });
  }, [mode, params.publicationId]);

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();

    if (!token) {
      setError("You must be logged in to publish.");
      return;
    }

    setPending(true);
    setError(null);

    try {
      const api = createApiClient();

      if (mode === "create") {
        await api.createPublication(token, { title, text });
      } else {
        await api.updatePublication(token, params.publicationId, {
          title,
          text,
        });
      }

      router.push("/");
    } catch (caught) {
      setError(
        caught instanceof Error ? caught.message : "Publication save failed.",
      );
    } finally {
      setPending(false);
    }
  }

  if (loading) {
    return <p className="text-on-surface-variant">Loading publication...</p>;
  }

  return (
    <form className="space-y-8" onSubmit={onSubmit}>
      <div className="space-y-2">
        <label
          className="text-sm font-bold uppercase tracking-wider text-on-surface-variant"
          htmlFor="publication-title"
        >
          Title
        </label>
        <input
          id="publication-title"
          value={title}
          onChange={(event) => setTitle(event.target.value)}
          className="w-full rounded-xl border-2 border-surface-variant/30 bg-surface-container-low px-4 py-4 text-xl font-headline font-bold outline-none"
          required
        />
      </div>
      <div className="space-y-2">
        <label
          className="text-sm font-bold uppercase tracking-wider text-on-surface-variant"
          htmlFor="publication-body"
        >
          Body
        </label>
        <textarea
          id="publication-body"
          value={text}
          onChange={(event) => setText(event.target.value)}
          className="w-full rounded-xl border-2 border-surface-variant/30 bg-surface-container-low p-4 outline-none"
          rows={8}
          required
        />
      </div>
      {error ? <p className="text-sm text-error">{error}</p> : null}
      <button
        type="submit"
        disabled={pending}
        className="rounded-2xl bg-primary-gradient px-8 py-4 text-lg font-extrabold text-white disabled:opacity-60"
      >
        {pending ? "Saving..." : mode === "create" ? "Publish" : "Save Changes"}
      </button>
    </form>
  );
}
