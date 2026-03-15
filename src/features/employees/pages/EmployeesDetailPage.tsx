import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router-dom";
import { useToast } from "../../../app/providers/ToastProvider";
import { Button } from "../../../components/ui/button";
import { getApiErrorMessage } from "../../../lib/apiError";
import { apiClient } from "../../../services/apiClient";
import type { ApiListResponse } from "../../../types/api.types";
import type { UserRecord } from "../../users/types/user.types";
import { EmployeeFormSection } from "../components/EmployeeFormSection";
import { employeeService } from "../services/employeeService";
import type { DepartmentRecord, PositionRecord } from "../types/employee.types";

export function EmployeesDetailPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const queryClient = useQueryClient();
  const toast = useToast();
  const [form, setForm] = useState({
    user_id: "",
    employee_code: "",
    department_id: "",
    position_id: "",
    phone: "",
    hire_date: "",
    status: "active",
  });

  const query = useQuery({
    queryKey: ["employee-detail", id],
    queryFn: () => employeeService.getEmployee(id as string),
    enabled: typeof id === "string" && id.length > 0,
  });

  const optionsQuery = useQuery({
    queryKey: ["employee-options"],
    queryFn: async () => {
      const [usersResponse, departmentsResponse, positionsResponse] = await Promise.all([
        apiClient.get<ApiListResponse<UserRecord>>("/users?per_page=200"),
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

  useEffect(() => {
    if (!query.data) {
      return;
    }

    setForm({
      user_id: query.data.user_id,
      employee_code: query.data.employee_code,
      department_id: query.data.department_id,
      position_id: query.data.position_id,
      phone: query.data.phone ?? "",
      hire_date: query.data.hire_date ?? "",
      status: query.data.status ?? "active",
    });
  }, [query.data]);

  const updateMutation = useMutation({
    mutationFn: () => employeeService.updateEmployee(id as string, form),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["employees"] });
      await queryClient.invalidateQueries({ queryKey: ["employee-detail", id] });
      toast.success("Employee berhasil diupdate.");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal mengupdate employee."));
    },
  });

  const deleteMutation = useMutation({
    mutationFn: () => employeeService.deleteEmployee(id as string),
    onSuccess: async () => {
      await queryClient.invalidateQueries({ queryKey: ["employees"] });
      toast.success("Employee berhasil dihapus.");
      navigate("/employees");
    },
    onError: (error) => {
      toast.error(getApiErrorMessage(error, "Gagal menghapus employee."));
    },
  });

  if (query.isLoading) {
    return <p className="text-sm text-muted-foreground">Loading employee detail...</p>;
  }

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Employee Detail</h1>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Edit Profile</h2>
        <form
          className="space-y-4"
          onSubmit={(event) => {
            event.preventDefault();
            updateMutation.mutate();
          }}
        >
          <EmployeeFormSection
            value={form}
            users={optionsQuery.data?.users ?? []}
            departments={optionsQuery.data?.departments ?? []}
            positions={optionsQuery.data?.positions ?? []}
            onChange={(next) => setForm((previous) => ({ ...previous, ...next }))}
          />

          <div className="flex flex-wrap gap-3">
            <Button type="submit" disabled={updateMutation.isPending}>Save Changes</Button>
            <Button
              type="button"
              variant="outline"
              disabled={deleteMutation.isPending}
              onClick={() => {
                if (window.confirm("Delete this employee? This action cannot be undone.")) {
                  deleteMutation.mutate();
                }
              }}
            >
              Delete Employee
            </Button>
            <Link to="/employees"><Button type="button" variant="outline">Back to List</Button></Link>
          </div>
        </form>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Barcode</h2>
        <div className="flex h-28 items-center justify-center rounded-xl border border-dashed border-border">Barcode Image / Value</div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Attendance Summary</h2>
        <div className="grid gap-3 md:grid-cols-3">
          <article className="rounded-xl border border-border p-4"><p className="text-sm text-muted-foreground">Total Present</p><p className="text-3xl font-semibold">-</p></article>
          <article className="rounded-xl border border-border p-4"><p className="text-sm text-muted-foreground">Total Late</p><p className="text-3xl font-semibold">-</p></article>
          <article className="rounded-xl border border-border p-4"><p className="text-sm text-muted-foreground">Total Absent</p><p className="text-3xl font-semibold">-</p></article>
        </div>
      </section>

      <div className="flex gap-3">
        <Button variant="outline">Generate Barcode</Button>
      </div>
    </section>
  );
}
