import { useQuery } from "@tanstack/react-query";
import { useParams } from "react-router-dom";
import { Button } from "../../../components/ui/button";
import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import { activityLogService } from "../../activity-logs/services/activityLogService";
import type { ActivityLogRecord } from "../../activity-logs/types/activity-log.types";
import type { EmployeeRecord } from "../../employees/types/employee.types";
import { attendanceService } from "../../attendance/services/attendanceService";
import type { RoleRecord, UserRecord, UserRoleRecord } from "../types/user.types";

export function UsersDetailPage() {
  const { id } = useParams();
  const query = useQuery({
    queryKey: ["users-detail", id],
    queryFn: async () => {
      const [userResponse, userRolesResponse, rolesResponse, employeesResponse, attendances, activityLogs] = await Promise.all([
        apiClient.get<ApiDetailResponse<UserRecord>>(`/users/${id}`),
        apiClient.get<ApiListResponse<UserRoleRecord>>("/user-roles?per_page=200"),
        apiClient.get<ApiListResponse<RoleRecord>>("/roles?per_page=100"),
        apiClient.get<ApiListResponse<EmployeeRecord>>("/employees?per_page=100"),
        attendanceService.getAttendances(),
        activityLogService.getActivityLogs(),
      ]);

      const user = userResponse.data.data;
      const userRole = userRolesResponse.data.data.find((relation) => relation.user_id === user.id);
      const role = rolesResponse.data.data.find((item) => item.id === userRole?.role_id)?.name ?? "-";
      const employee = employeesResponse.data.data.find((item) => item.user_id === user.id);
      const attendanceHistory = attendances.filter((item) => item.employee === user.name).slice(0, 10);
      const activity = activityLogs.filter((item: ActivityLogRecord) => item.user_id === user.id).slice(0, 10);

      return {
        user,
        role,
        employee,
        attendanceHistory,
        activity,
      };
    },
    enabled: typeof id === "string" && id.length > 0,
  });

  if (query.isLoading) {
    return <p className="text-sm text-muted-foreground">Loading user detail...</p>;
  }

  const detail = query.data;

  return (
    <section className="space-y-6">
      <header>
        <h1 className="text-2xl font-semibold">User Detail</h1>
      </header>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Profile</h2>
        <div className="grid gap-3 md:grid-cols-2 text-sm">
          <p><span className="text-muted-foreground">Name:</span> {detail?.user.name ?? "-"}</p>
          <p><span className="text-muted-foreground">Email:</span> {detail?.user.email ?? "-"}</p>
          <p><span className="text-muted-foreground">Employee Code:</span> {detail?.employee?.employee_code ?? "-"}</p>
          <p><span className="text-muted-foreground">Role:</span> {detail?.role ?? "-"}</p>
          <p><span className="text-muted-foreground">Status:</span> {detail?.user.is_active ? "Active" : "Inactive"}</p>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Barcode</h2>
        <div className="grid gap-3 text-sm md:grid-cols-2">
          <div className="flex h-24 items-center justify-center rounded-xl border border-dashed border-border">Barcode Image</div>
          <div className="space-y-2">
            <p><span className="text-muted-foreground">Barcode Value:</span> ABS-EMP-1001</p>
            <p><span className="text-muted-foreground">Created Date:</span> -</p>
            <p><span className="text-muted-foreground">Last Scan:</span> -</p>
            <Button size="sm" variant="outline">Regenerate Barcode</Button>
          </div>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Attendance History</h2>
        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="w-full border-collapse text-sm">
            <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
              <tr>
                <th className="px-4 py-3">Date</th>
                <th className="px-4 py-3">Check In</th>
                <th className="px-4 py-3">Check Out</th>
                <th className="px-4 py-3">Status</th>
                <th className="px-4 py-3">Location</th>
              </tr>
            </thead>
            <tbody>
              {detail?.attendanceHistory.map((item) => (
                <tr key={item.date} className="border-t border-border">
                  <td className="px-4 py-3">{item.date}</td>
                  <td className="px-4 py-3">{item.checkIn}</td>
                  <td className="px-4 py-3">{item.checkOut}</td>
                  <td className="px-4 py-3">{item.status}</td>
                  <td className="px-4 py-3">-</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-4 text-lg font-semibold">Activity Log</h2>
        <div className="overflow-x-auto rounded-xl border border-border">
          <table className="w-full border-collapse text-sm">
            <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
              <tr>
                <th className="px-4 py-3">Action</th>
                <th className="px-4 py-3">Description</th>
                <th className="px-4 py-3">IP Address</th>
                <th className="px-4 py-3">Time</th>
              </tr>
            </thead>
            <tbody>
              {detail?.activity.map((item) => (
                <tr key={item.id} className="border-t border-border">
                  <td className="px-4 py-3">{item.action}</td>
                  <td className="px-4 py-3">{item.description ?? "-"}</td>
                  <td className="px-4 py-3">{item.ip_address ?? "-"}</td>
                  <td className="px-4 py-3">{item.created_at}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      </section>
    </section>
  );
}
