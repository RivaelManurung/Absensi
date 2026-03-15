import { useQuery } from "@tanstack/react-query";
import { employeeService } from "../services/employeeService";
import type { EmployeesFilters } from "../types/employee.types";

export function useEmployees(filters: EmployeesFilters) {
  return useQuery({
    queryKey: ["employees", filters],
    queryFn: () =>
      employeeService.getEmployees({
        query: filters.query,
      }),
  });
}
