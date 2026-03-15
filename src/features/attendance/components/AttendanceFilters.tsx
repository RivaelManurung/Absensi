import { Input } from "../../../components/ui/input";
import type { AttendanceFilters } from "../types/attendance.types";

type AttendanceFiltersProps = {
  value: AttendanceFilters;
  onChange: (next: AttendanceFilters) => void;
};

export function AttendanceFiltersPanel({ value, onChange }: AttendanceFiltersProps) {
  return (
    <div className="grid gap-3 md:grid-cols-2">
      <Input type="date" value={value.date} onChange={(event) => onChange({ ...value, date: event.target.value })} />
      <Input
        placeholder="Search employee or status"
        value={value.query}
        onChange={(event) => onChange({ ...value, query: event.target.value })}
      />
    </div>
  );
}
