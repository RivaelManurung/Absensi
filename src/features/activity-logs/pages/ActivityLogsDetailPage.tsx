import { useQuery } from "@tanstack/react-query";
import { useParams } from "react-router-dom";
import { activityLogService } from "../services/activityLogService";

export function ActivityLogsDetailPage() {
  const { id } = useParams();
  const detailQuery = useQuery({
    queryKey: ["activity-log", id],
    queryFn: () => activityLogService.getActivityLog(id ?? ""),
    enabled: typeof id === "string" && id.length > 0,
  });

  const log = detailQuery.data;

  if (detailQuery.isLoading) {
    return <p className="text-sm text-muted-foreground">Loading activity detail...</p>;
  }

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Activity Detail</h1>

      <section className="rounded-2xl border border-border bg-card p-5 text-sm">
        <div className="grid gap-2 md:grid-cols-2">
          <p><span className="text-muted-foreground">User:</span> {log?.user_name ?? "System"}</p>
          <p><span className="text-muted-foreground">Action:</span> {log?.action ?? "-"}</p>
          <p><span className="text-muted-foreground">Module:</span> {log?.module ?? "-"}</p>
          <p><span className="text-muted-foreground">IP Address:</span> {log?.ip_address ?? "-"}</p>
          <p><span className="text-muted-foreground">User Agent:</span> {log?.user_agent ?? "-"}</p>
          <p><span className="text-muted-foreground">Timestamp:</span> {log?.created_at ?? "-"}</p>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5 text-sm">
        <h2 className="mb-3 text-lg font-semibold">Description</h2>
        <p>{log?.description ?? "-"}</p>
      </section>

      <section className="grid gap-4 md:grid-cols-2">
        <article className="rounded-2xl border border-border bg-card p-5 text-sm">
          <h2 className="mb-3 text-lg font-semibold">Meta</h2>
          <pre className="overflow-auto rounded-xl bg-muted p-3">{JSON.stringify(log?.meta ?? {}, null, 2)}</pre>
        </article>
      </section>
    </section>
  );
}
