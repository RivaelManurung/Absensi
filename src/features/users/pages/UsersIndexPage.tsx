import { useState } from "react";
import { Link } from "react-router-dom";
import { Button } from "../../../components/ui/button";
import { APP_ROUTES } from "../../../lib/constants";
import { useUsers } from "../hooks/useUsers";
import { UsersFilters as UsersFiltersPanel } from "../components/UsersFilters";
import { UsersTable } from "../components/UsersTable";
import type { UsersFilters } from "../types/user.types";

export function UsersIndexPage() {
  const [filters, setFilters] = useState<UsersFilters>({ query: "", role: "all", status: "all" });
  const query = useUsers(filters);
  const roles = [...new Set((query.data ?? []).map((item) => item.role).filter((role) => role !== "-"))];

  return (
    <section className="space-y-6">
      <header className="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h1 className="text-2xl font-semibold">User Management</h1>
          <p className="text-sm text-muted-foreground">Kelola akun user aplikasi absensi.</p>
        </div>
        <Link to={APP_ROUTES.usersCreate}>
          <Button>Create User</Button>
        </Link>
      </header>

      <UsersFiltersPanel value={filters} roles={roles} onChange={setFilters} />

      {query.isLoading ? <p className="text-sm text-muted-foreground">Loading users...</p> : <UsersTable rows={query.data ?? []} />}
    </section>
  );
}
