import { apiClient } from "../../../services/apiClient";
import type { ApiDetailResponse, ApiListResponse } from "../../../types/api.types";
import type { EmployeeRecord } from "../../employees/types/employee.types";
import type { AttendanceListItem, AttendanceLogRecord, AttendanceRecord } from "../types/attendance.types";

type AttendanceListParams = {
  query?: string;
  date?: string;
  status?: string;
  employee_id?: string;
  per_page?: number;
};

function createQueryString(params: AttendanceListParams) {
  const query = new URLSearchParams();

  query.set("per_page", String(params.per_page ?? 100));

  if (params.query && params.query.trim().length > 0) {
    query.set("query", params.query.trim());
  }

  if (params.date) {
    query.set("date", params.date);
  }

  if (params.status && params.status !== "all") {
    query.set("status", params.status);
  }

  if (params.employee_id) {
    query.set("employee_id", params.employee_id);
  }

  return query.toString();
}

export const attendanceService = {
  async getAttendances(params: AttendanceListParams = {}): Promise<AttendanceListItem[]> {
    const queryString = createQueryString(params);
    const [attendanceResponse, employeesResponse, attendanceLogsResponse] = await Promise.all([
      apiClient.get<ApiListResponse<AttendanceRecord>>(`/attendances?${queryString}`),
      apiClient.get<ApiListResponse<EmployeeRecord>>("/employees?per_page=200"),
      apiClient.get<ApiListResponse<AttendanceLogRecord>>("/attendance-logs?per_page=200"),
    ]);

    const employeeById = new Map(employeesResponse.data.data.map((employee) => [employee.id, employee]));
    const firstLogByAttendanceId = new Map<string, AttendanceLogRecord>();

    for (const log of attendanceLogsResponse.data.data) {
      if (!firstLogByAttendanceId.has(log.attendance_id)) {
        firstLogByAttendanceId.set(log.attendance_id, log);
      }
    }

    return attendanceResponse.data.data.map((attendance) => {
      const employee = employeeById.get(attendance.employee_id);
      const employeeName = employee?.user_name ?? "-";
      const firstLog = firstLogByAttendanceId.get(attendance.id);

      return {
        id: attendance.id,
        employee: employeeName,
        date: attendance.attendance_date,
        checkIn: attendance.check_in_time?.slice(11, 16) ?? "-",
        checkOut: attendance.check_out_time?.slice(11, 16) ?? "-",
        device: firstLog?.device_info ?? attendance.check_in_method ?? "-",
        status: attendance.status ?? "-",
      };
    });
  },

  async getAttendance(id: string): Promise<AttendanceRecord> {
    const response = await apiClient.get<ApiDetailResponse<AttendanceRecord>>(`/attendances/${id}`);
    return response.data.data;
  },
};
