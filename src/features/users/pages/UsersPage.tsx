import { useUsers } from "../hooks/useUsers";
import { UserTable } from "../components/UserTable";

export function UsersPage() {
  const { data, isLoading } = useUsers({ query: "", role: "all", status: "all" });

  return (
    <section className="space-y-4">
      <header>
        <h1 className="text-2xl font-semibold">Users</h1>
        <p className="text-sm text-muted-foreground">Manage all employees in one place.</p>
      </header>
      {isLoading ? <p className="text-sm text-muted-foreground">Loading users...</p> : <UserTable users={data ?? []} />}
    </section>
  );
}
