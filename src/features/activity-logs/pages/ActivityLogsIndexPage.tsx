import { useState } from "react";
import { ActivityLogsFilters } from "../components/ActivityLogsFilters";
import { ActivityLogsTable } from "../components/ActivityLogsTable";
import { useActivityLogs } from "../hooks/useActivityLogs";

export function ActivityLogsIndexPage() {
  const [filters, setFilters] = useState({ query: "", module: "", action: "", date: "" });
  const query = useActivityLogs(filters);

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Activity Log</h1>

      <ActivityLogsFilters value={filters} onChange={setFilters} />

      {query.isLoading ? <p className="text-sm text-muted-foreground">Loading activity logs...</p> : <ActivityLogsTable rows={query.data ?? []} />}
    </section>
  );
}
