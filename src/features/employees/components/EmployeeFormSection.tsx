import { Input } from "../../../components/ui/input";
import type { DepartmentRecord, PositionRecord } from "../types/employee.types";
import type { UserRecord } from "../../users/types/user.types";

type EmployeeFormState = {
  user_id: string;
  employee_code: string;
  department_id: string;
  position_id: string;
  phone: string;
  hire_date: string;
  status: string;
};

type EmployeeFormSectionProps = {
  value: EmployeeFormState;
  users: UserRecord[];
  departments: DepartmentRecord[];
  positions: PositionRecord[];
  onChange: (next: Partial<EmployeeFormState>) => void;
};

export function EmployeeFormSection({ value, users, departments, positions, onChange }: EmployeeFormSectionProps) {
  return (
    <div className="grid gap-4 md:grid-cols-2">
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.user_id}
        onChange={(event) => onChange({ user_id: event.target.value })}
      >
        <option value="">Select User</option>
        {users.map((user) => (
          <option key={user.id} value={user.id}>
            {user.name} ({user.email})
          </option>
        ))}
      </select>
      <Input
        placeholder="Employee Code"
        value={value.employee_code}
        onChange={(event) => onChange({ employee_code: event.target.value })}
      />
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.department_id}
        onChange={(event) => onChange({ department_id: event.target.value })}
      >
        <option value="">Select Department</option>
        {departments.map((department) => (
          <option key={department.id} value={department.id}>
            {department.name}
          </option>
        ))}
      </select>
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.position_id}
        onChange={(event) => onChange({ position_id: event.target.value })}
      >
        <option value="">Select Position</option>
        {positions.map((position) => (
          <option key={position.id} value={position.id}>
            {position.name}
          </option>
        ))}
      </select>
      <Input placeholder="Phone" value={value.phone} onChange={(event) => onChange({ phone: event.target.value })} />
      <Input
        placeholder="Hire Date"
        type="date"
        value={value.hire_date}
        onChange={(event) => onChange({ hire_date: event.target.value })}
      />
      <Input
        placeholder="Status"
        value={value.status}
        onChange={(event) => onChange({ status: event.target.value })}
      />
    </div>
  );
}
