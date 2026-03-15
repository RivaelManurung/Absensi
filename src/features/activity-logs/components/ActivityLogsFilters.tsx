import { Input } from "../../../components/ui/input";
import type { ActivityLogFilters } from "../types/activity-log.types";

type ActivityLogsFiltersProps = {
  value: ActivityLogFilters;
  onChange: (next: ActivityLogFilters) => void;
};

export function ActivityLogsFilters({ value, onChange }: ActivityLogsFiltersProps) {
  return (
    <div className="grid gap-3 md:grid-cols-4">
      <Input
        placeholder="Search user"
        value={value.query}
        onChange={(event) => onChange({ ...value, query: event.target.value })}
      />
      <Input
        placeholder="Filter module"
        value={value.module}
        onChange={(event) => onChange({ ...value, module: event.target.value })}
      />
      <Input
        placeholder="Filter action"
        value={value.action}
        onChange={(event) => onChange({ ...value, action: event.target.value })}
      />
      <Input type="date" value={value.date} onChange={(event) => onChange({ ...value, date: event.target.value })} />
    </div>
  );
}
