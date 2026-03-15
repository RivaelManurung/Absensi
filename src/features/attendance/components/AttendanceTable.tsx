import { Link } from "react-router-dom";
import type { AttendanceListItem } from "../types/attendance.types";

type AttendanceTableProps = {
  rows: AttendanceListItem[];
};

export function AttendanceTable({ rows }: AttendanceTableProps) {
  return (
    <div className="overflow-x-auto rounded-2xl border border-border bg-card">
      <table className="w-full border-collapse text-sm">
        <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
          <tr>
            <th className="px-4 py-3">Employee</th>
            <th className="px-4 py-3">Date</th>
            <th className="px-4 py-3">Check In</th>
            <th className="px-4 py-3">Check Out</th>
            <th className="px-4 py-3">Device</th>
            <th className="px-4 py-3">Status</th>
            <th className="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="border-t border-border">
              <td className="px-4 py-3">{row.employee}</td>
              <td className="px-4 py-3">{row.date}</td>
              <td className="px-4 py-3">{row.checkIn}</td>
              <td className="px-4 py-3">{row.checkOut}</td>
              <td className="px-4 py-3">{row.device}</td>
              <td className="px-4 py-3">{row.status}</td>
              <td className="px-4 py-3">
                <Link to={`/attendance/${row.id}`} className="rounded-md border border-border px-2 py-1 text-xs">
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
