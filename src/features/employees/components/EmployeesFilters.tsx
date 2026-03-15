import { Input } from "../../../components/ui/input";
import type { EmployeesFilters } from "../types/employee.types";

type EmployeesFiltersProps = {
  value: EmployeesFilters;
  onChange: (next: EmployeesFilters) => void;
};

export function EmployeesFiltersPanel({ value, onChange }: EmployeesFiltersProps) {
  return (
    <Input
      placeholder="Search by employee code, name, or department"
      value={value.query}
      onChange={(event) => onChange({ ...value, query: event.target.value })}
    />
  );
}
