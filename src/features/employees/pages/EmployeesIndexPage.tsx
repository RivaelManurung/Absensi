import { Link } from "react-router-dom";
import { Button } from "../../../components/ui/button";
import { useState } from "react";
import { EmployeesFiltersPanel } from "../components/EmployeesFilters";
import { EmployeesTable } from "../components/EmployeesTable";
import { useEmployees } from "../hooks/useEmployees";

export function EmployeesIndexPage() {
  const [filters, setFilters] = useState({ query: "" });
  const query = useEmployees(filters);

  return (
    <section className="space-y-6">
      <header className="flex flex-wrap items-center justify-between gap-3">
        <h1 className="text-2xl font-semibold">Employee Management</h1>
        <Link to="/employees/create"><Button>Add Employee</Button></Link>
      </header>

      <EmployeesFiltersPanel value={filters} onChange={setFilters} />

      {query.isLoading ? (
        <p className="text-sm text-muted-foreground">Loading employees...</p>
      ) : (
        <EmployeesTable rows={query.data ?? []} />
      )}
    </section>
  );
}
