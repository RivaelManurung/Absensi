const rows = [
  { name: "users.create", module: "Users", description: "Can create users" },
  { name: "users.edit", module: "Users", description: "Can edit users" },
  { name: "attendance.view", module: "Attendance", description: "Can view attendance logs" },
];

export function PermissionsIndexPage() {
  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Permission Management</h1>
      <div className="overflow-x-auto rounded-2xl border border-border bg-card">
        <table className="w-full border-collapse text-sm">
          <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
            <tr>
              <th className="px-4 py-3">Permission Name</th>
              <th className="px-4 py-3">Module</th>
              <th className="px-4 py-3">Description</th>
            </tr>
          </thead>
          <tbody>
            {rows.map((row) => (
              <tr key={row.name} className="border-t border-border">
                <td className="px-4 py-3 font-medium">{row.name}</td>
                <td className="px-4 py-3">{row.module}</td>
                <td className="px-4 py-3">{row.description}</td>
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </section>
  );
}
