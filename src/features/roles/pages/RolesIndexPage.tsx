const rows = [
  { name: "Admin", description: "Full access to system", usersCount: 3 },
  { name: "Supervisor", description: "Manage attendance and users", usersCount: 8 },
  { name: "Employee", description: "View personal attendance", usersCount: 237 },
];

export function RolesIndexPage() {
  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Role Management</h1>
      <div className="overflow-x-auto rounded-2xl border border-border bg-card">
        <table className="w-full border-collapse text-sm">
          <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
            <tr>
              <th className="px-4 py-3">Role Name</th>
              <th className="px-4 py-3">Description</th>
              <th className="px-4 py-3">Users Count</th>
              <th className="px-4 py-3">Actions</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.name} className="border-t border-border">
                <td className="px-4 py-3 font-medium">{row.name}</td>
                <td className="px-4 py-3">{row.description}</td>
                <td className="px-4 py-3">{row.usersCount}</td>
                <td className="px-4 py-3">
                  <div className="flex gap-2 text-xs">
                    <button className="rounded-md border border-border px-2 py-1">View</button>
                    <button className="rounded-md border border-border px-2 py-1">Edit</button>
                    <button className="rounded-md border border-border px-2 py-1">Delete</button>
                  </div>
                </td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  );
}
