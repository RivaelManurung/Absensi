import { useQuery } from "@tanstack/react-query";
import { attendanceService } from "../services/attendanceService";
import type { AttendanceFilters } from "../types/attendance.types";

export function useAttendances(filters: AttendanceFilters) {
  return useQuery({
    queryKey: ["attendances", filters],
    queryFn: () =>
      attendanceService.getAttendances({
        query: filters.query,
        date: filters.date,
      }),
  });
}
