import { useQuery } from "@tanstack/react-query";
import { useParams } from "react-router-dom";
import { apiClient } from "../../../services/apiClient";
import type { ApiListResponse } from "../../../types/api.types";
import type { EmployeeRecord } from "../../employees/types/employee.types";
import type { UserRecord } from "../../users/types/user.types";
import { attendanceService } from "../services/attendanceService";
import type { AttendanceLogRecord } from "../types/attendance.types";

export function AttendanceDetailPage() {
  const { id } = useParams();

  const detailQuery = useQuery({
    queryKey: ["attendance", id],
    queryFn: () => attendanceService.getAttendance(id ?? ""),
    enabled: typeof id === "string" && id.length > 0,
  });

  const relatedQuery = useQuery({
    queryKey: ["attendance-related"],
    queryFn: async () => {
      const [employeesResponse, usersResponse, logsResponse] = await Promise.all([
        apiClient.get<ApiListResponse<EmployeeRecord>>("/employees?per_page=100"),
        apiClient.get<ApiListResponse<UserRecord>>("/users?per_page=100"),
        apiClient.get<ApiListResponse<AttendanceLogRecord>>("/attendance-logs?per_page=200"),
      ]);

      return {
        employees: employeesResponse.data.data,
        users: usersResponse.data.data,
        logs: logsResponse.data.data,
      };
    },
  });

  const attendance = detailQuery.data;
  const employee = relatedQuery.data?.employees.find((item) => item.id === attendance?.employee_id);
  const user = relatedQuery.data?.users.find((item) => item.id === employee?.user_id);
  const logs = relatedQuery.data?.logs.filter((item) => item.attendance_id === attendance?.id) ?? [];
  const checkInLog = logs.find((item) => item.type.toLowerCase().includes("in"));
  const checkOutLog = logs.find((item) => item.type.toLowerCase().includes("out"));

  if (detailQuery.isLoading || relatedQuery.isLoading) {
    return <p className="text-sm text-muted-foreground">Loading attendance detail...</p>;
  }

  return (
    <section className="space-y-6">
      <h1 className="text-2xl font-semibold">Attendance Detail</h1>

      <section className="rounded-2xl border border-border bg-card p-5 text-sm">
        <h2 className="mb-3 text-lg font-semibold">Employee Info</h2>
        <div className="grid gap-2 md:grid-cols-2">
          <p><span className="text-muted-foreground">Employee Name:</span> {user?.name ?? "-"}</p>
          <p><span className="text-muted-foreground">Date:</span> {attendance?.attendance_date ?? "-"}</p>
          <p><span className="text-muted-foreground">Status:</span> {attendance?.status ?? "-"}</p>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5 text-sm">
        <h2 className="mb-3 text-lg font-semibold">Check In</h2>
        <div className="grid gap-2 md:grid-cols-2">
          <p><span className="text-muted-foreground">Time:</span> {attendance?.check_in_time?.slice(11, 16) ?? "-"}</p>
          <p><span className="text-muted-foreground">Method:</span> {attendance?.check_in_method ?? "-"}</p>
          <p><span className="text-muted-foreground">Device:</span> {checkInLog?.device_info ?? "-"}</p>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5 text-sm">
        <h2 className="mb-3 text-lg font-semibold">Check Out</h2>
        <div className="grid gap-2 md:grid-cols-2">
          <p><span className="text-muted-foreground">Time:</span> {attendance?.check_out_time?.slice(11, 16) ?? "-"}</p>
          <p><span className="text-muted-foreground">Method:</span> {attendance?.check_out_method ?? "-"}</p>
          <p><span className="text-muted-foreground">Device:</span> {checkOutLog?.device_info ?? "-"}</p>
        </div>
      </section>

      <section className="rounded-2xl border border-border bg-card p-5">
        <h2 className="mb-3 text-lg font-semibold">Map</h2>
        <div className="flex h-64 items-center justify-center rounded-xl border border-dashed border-border text-sm text-muted-foreground">
          Coordinate map integration can be added from check-in/check-out lat-lng fields.
        </div>
      </section>
    </section>
  );
}
