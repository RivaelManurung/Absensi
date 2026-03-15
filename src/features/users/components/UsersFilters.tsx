import { Input } from "../../../components/ui/input";
import type { UsersFilters } from "../types/user.types";

type UsersFiltersProps = {
  value: UsersFilters;
  roles: string[];
  onChange: (next: UsersFilters) => void;
};

export function UsersFilters({ value, roles, onChange }: UsersFiltersProps) {
  return (
    <div className="grid gap-3 md:grid-cols-3">
      <Input
        placeholder="Search by name or email"
        value={value.query}
        onChange={(event) => onChange({ ...value, query: event.target.value })}
      />
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.role}
        onChange={(event) => onChange({ ...value, role: event.target.value })}
      >
        <option value="all">All Roles</option>
        {roles.map((role) => (
          <option key={role} value={role}>
            {role}
          </option>
        ))}
      </select>
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.status}
        onChange={(event) =>
          onChange({
            ...value,
            status: event.target.value as UsersFilters["status"],
          })
        }
      >
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  );
}
