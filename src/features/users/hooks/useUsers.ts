import { useQuery } from "@tanstack/react-query";
import { getUsers } from "../api/getUsers";
import type { UsersFilters } from "../types/user.types";

export function useUsers(filters: UsersFilters) {
  return useQuery({
    queryKey: ["users", filters],
    queryFn: () =>
      getUsers({
        query: filters.query,
        role: filters.role,
        status: filters.status,
      }),
  });
}
