"use client";

import { useEffect, useMemo, useState } from "react";
import { useAuth } from "@/components/auth/use-auth";
import { ApiError, createApiClient } from "@/lib/api";
import type { Publication } from "@/lib/contracts";
import { PublicationCard } from "@/components/publications/publication-card";

export function PublicationFeed() {
  const { status, token, user } = useAuth();
  const [publications, setPublications] = useState<Publication[]>([]);
  const [viewState, setViewState] = useState<
    "loading" | "ready" | "empty" | "error"
  >("loading");
  const [error, setError] = useState<string | null>(null);

  useEffect(() => {
    const api = createApiClient();

    api.listPublications()
      .then((response) => {
        setPublications(response.data);
        setViewState(response.data.length === 0 ? "empty" : "ready");
      })
      .catch((caught) => {
        setError(
          caught instanceof Error
            ? caught.message
            : "Failed to load publications.",
        );
        setViewState("error");
      });
  }, []);

  const ownership = useMemo(
    () =>
      new Set(
        publications
          .filter((publication) => publication.author_id === user?.id)
          .map((publication) => publication.id),
      ),
    [publications, user?.id],
  );

  async function deletePublication(publicationId: string) {
    if (status !== "authenticated" || !token) {
      return;
    }

    if (!window.confirm("Delete this publication?")) {
      return;
    }

    try {
      const api = createApiClient();
      await api.deletePublication(token, publicationId);
      setPublications((current) =>
        current.filter((publication) => publication.id !== publicationId),
      );
    } catch (caught) {
      if (caught instanceof ApiError) {
        setError(caught.message);
        setViewState("error");
      }
    }
  }

  if (viewState === "loading") {
    return (
      <p className="mx-auto max-w-4xl p-8 text-on-surface-variant">
        Loading publications...
      </p>
    );
  }

  if (viewState === "error") {
    return <p className="mx-auto max-w-4xl p-8 text-error">{error}</p>;
  }

  if (viewState === "empty") {
    return (
      <p className="mx-auto max-w-4xl p-8 text-on-surface-variant">
        No publications yet.
      </p>
    );
  }

  return (
    <div className="max-w-4xl mx-auto flex flex-col gap-8">
      {publications.map((publication) => (
        <PublicationCard
          key={publication.id}
          publication={publication}
          canManage={ownership.has(publication.id)}
          onDelete={deletePublication}
        />
      ))}
    </div>
  );
}
