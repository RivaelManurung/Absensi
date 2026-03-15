import { Link } from "react-router-dom";
import type { ActivityLogRecord } from "../types/activity-log.types";

type ActivityLogsTableProps = {
  rows: ActivityLogRecord[];
};

export function ActivityLogsTable({ rows }: ActivityLogsTableProps) {
  return (
    <div className="overflow-x-auto rounded-2xl border border-border bg-card">
      <table className="w-full border-collapse text-sm">
        <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
          <tr>
            <th className="px-4 py-3">User</th>
            <th className="px-4 py-3">Action</th>
            <th className="px-4 py-3">Module</th>
            <th className="px-4 py-3">Description</th>
            <th className="px-4 py-3">IP Address</th>
            <th className="px-4 py-3">Time</th>
            <th className="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="border-t border-border">
              <td className="px-4 py-3">{row.user_name ?? "System"}</td>
              <td className="px-4 py-3">{row.action}</td>
              <td className="px-4 py-3">{row.module}</td>
              <td className="px-4 py-3">{row.description ?? "-"}</td>
              <td className="px-4 py-3">{row.ip_address ?? "-"}</td>
              <td className="px-4 py-3">{row.created_at}</td>
              <td className="px-4 py-3">
                <Link to={`/activity-logs/${row.id}`} className="rounded-md border border-border px-2 py-1 text-xs">
                  View Detail
                </Link>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
