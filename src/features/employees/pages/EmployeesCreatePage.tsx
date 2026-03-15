import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useState } from "react";
import { Link } from "react-router-dom";
import { useToast } from "../../../app/providers/ToastProvider";
import { Button } from "../../../components/ui/button";
import { getApiErrorMessage } from "../../../lib/apiError";
import { apiClient } from "../../../services/apiClient";
import type { ApiListResponse } from "../../../types/api.types";
import type { UserRecord } from "../../users/types/user.types";
import { EmployeeFormSection } from "../components/EmployeeFormSection";
import { employeeService } from "../services/employeeService";
import type { DepartmentRecord, PositionRecord } from "../types/employee.types";

export function EmployeesCreatePage() {
  const [form, setForm] = useState({
    user_id: "",
    employee_code: "",
    department_id: "",
    position_id: "",
    phone: "",
    hire_date: "",
    status: "active",
  });
  const queryClient = useQueryClient();
  const toast = useToast();

  const optionsQuery = useQuery({
    queryKey: ["employee-create-options"],
    queryFn: async () => {
      const [usersResponse, departmentsResponse, positionsResponse] = await Promise.all([
        apiClient.get<ApiListResponse<UserRecord>>("/users?per_page=100"),
        apiClient.get<ApiListResponse<DepartmentRecord>>("/departments?per_page=100"),
        apiClient.get<ApiListResponse<PositionRecord>>("/positions?per_page=100"),
      ]);

      return {
        users: usersResponse.data.data,
        departments: departmentsResponse.data.data,
        positions: positionsResponse.data.data,
      };
    },
  });

  const createMutation = useMutation({
    mutationFn: () => employeeService.createEmployee(form),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["employees"] });
      toast.success("Employee berhasil dibuat.");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal membuat employee."));
    },
  });

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Create Employee</h1>
      <form
        className="grid gap-4 rounded-2xl border border-border bg-card p-5"
        onSubmit={(event) => {
          event.preventDefault();
          createMutation.mutate();
        }}
      >
        <EmployeeFormSection
          value={form}
          users={optionsQuery.data?.users ?? []}
          departments={optionsQuery.data?.departments ?? []}
          positions={optionsQuery.data?.positions ?? []}
          onChange={(next) => setForm((previous) => ({ ...previous, ...next }))}
        />
        <div className="flex gap-3">
          <Button type="submit" disabled={createMutation.isPending}>Save</Button>
          <Link to="/employees"><Button variant="outline" type="button">Cancel</Button></Link>
        </div>
      </form>
    </section>
  );
}
