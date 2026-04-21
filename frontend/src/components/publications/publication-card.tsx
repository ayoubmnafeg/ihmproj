import Link from "next/link";
import type { Publication } from "@/lib/contracts";

export function PublicationCard({
  publication,
  canManage,
  onDelete,
}: {
  publication: Publication;
  canManage: boolean;
  onDelete: (publicationId: string) => void;
}) {
  return (
    <article className="bg-surface-container-lowest rounded-xl p-6 shadow-[0_1px_3px_rgba(0,0,0,0.05)] border border-surface-container-low/50">
      <div className="flex items-center justify-between mb-4">
        <div>
          <p className="text-sm font-bold text-on-surface">
            {publication.author?.profile?.display_name ??
              publication.author?.email ??
              "Anonymous"}
          </p>
          <p className="text-xs text-on-surface-variant">
            {new Date(publication.created_at).toLocaleString()}
          </p>
        </div>
        {canManage ? (
          <div className="flex items-center gap-3">
            <Link
              href={`/publications/${publication.id}/edit`}
              className="text-sm font-semibold text-primary"
            >
              Edit
            </Link>
            <button
              type="button"
              onClick={() => onDelete(publication.id)}
              className="text-sm font-semibold text-error"
            >
              Delete
            </button>
          </div>
        ) : null}
      </div>
      <h2 className="text-2xl font-bold mb-3 headline">{publication.title}</h2>
      <p className="text-on-surface-variant leading-relaxed">
        {publication.text}
      </p>
    </article>
  );
}
