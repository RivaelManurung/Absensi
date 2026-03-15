import { useState } from "react";
import { AttendanceFiltersPanel } from "../components/AttendanceFilters";
import { AttendanceTable } from "../components/AttendanceTable";
import { useAttendances } from "../hooks/useAttendances";

export function AttendanceIndexPage() {
  const [filters, setFilters] = useState({ date: "", query: "" });
  const query = useAttendances(filters);

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Attendance Log</h1>

      <AttendanceFiltersPanel value={filters} onChange={setFilters} />

      {query.isLoading ? <p className="text-sm text-muted-foreground">Loading attendance logs...</p> : <AttendanceTable rows={query.data ?? []} />}
    </section>
  );
}
