import { Link } from "react-router-dom";
import type { EmployeeListItem } from "../types/employee.types";

type EmployeesTableProps = {
  rows: EmployeeListItem[];
};

export function EmployeesTable({ rows }: EmployeesTableProps) {
  return (
    <div className="overflow-x-auto rounded-2xl border border-border bg-card">
      <table className="w-full border-collapse text-sm">
        <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
          <tr>
            <th className="px-4 py-3">Employee Code</th>
            <th className="px-4 py-3">Name</th>
            <th className="px-4 py-3">Department</th>
            <th className="px-4 py-3">Position</th>
            <th className="px-4 py-3">Status</th>
            <th className="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((employee) => (
            <tr key={employee.id} className="border-t border-border">
              <td className="px-4 py-3">{employee.code}</td>
              <td className="px-4 py-3 font-medium">{employee.name}</td>
              <td className="px-4 py-3">{employee.department}</td>
              <td className="px-4 py-3">{employee.position}</td>
              <td className="px-4 py-3">{employee.status}</td>
              <td className="px-4 py-3">
                <div className="flex flex-wrap gap-2 text-xs">
                  <Link to={`/employees/${employee.id}`} className="rounded-md border border-border px-2 py-1">
                    View
                  </Link>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
