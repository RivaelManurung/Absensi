import { Link } from "react-router-dom";
import type { UserListItem } from "../types/user.types";

type UsersTableProps = {
  rows: UserListItem[];
};

export function UsersTable({ rows }: UsersTableProps) {
  return (
    <div className="overflow-x-auto rounded-2xl border border-border bg-card">
      <table className="w-full border-collapse text-sm">
        <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
          <tr>
            <th className="px-4 py-3">Name</th>
            <th className="px-4 py-3">Email</th>
            <th className="px-4 py-3">Role</th>
            <th className="px-4 py-3">Status</th>
            <th className="px-4 py-3">Created Date</th>
            <th className="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((user) => (
            <tr key={user.id} className="border-t border-border align-top">
              <td className="px-4 py-3 font-medium">{user.name}</td>
              <td className="px-4 py-3 text-muted-foreground">{user.email}</td>
              <td className="px-4 py-3">{user.role}</td>
              <td className="px-4 py-3">{user.status}</td>
              <td className="px-4 py-3">{user.createdDate}</td>
              <td className="px-4 py-3">
                <div className="flex flex-wrap gap-2 text-xs">
                  <Link to={`/users/${user.id}`} className="rounded-md border border-border px-2 py-1">
                    View Detail
                  </Link>
                  <Link to={`/users/${user.id}/edit`} className="rounded-md border border-border px-2 py-1">
                    Edit
                  </Link>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
