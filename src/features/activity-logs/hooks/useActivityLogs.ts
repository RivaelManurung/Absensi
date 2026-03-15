import { useQuery } from "@tanstack/react-query";
import { activityLogService } from "../services/activityLogService";
import type { ActivityLogFilters } from "../types/activity-log.types";

export function useActivityLogs(filters: ActivityLogFilters) {
  return useQuery({
    queryKey: ["activity-logs", filters],
    queryFn: () =>
      activityLogService.getActivityLogs({
        query: filters.query,
        module: filters.module,
        action: filters.action,
        date: filters.date,
      }),
  });
}
