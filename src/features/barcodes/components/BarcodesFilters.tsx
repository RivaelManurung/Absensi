import { Input } from "../../../components/ui/input";
import type { BarcodeListFilters } from "../types/barcode.types";

type BarcodesFiltersProps = {
  value: BarcodeListFilters;
  onChange: (next: BarcodeListFilters) => void;
};

export function BarcodesFilters({ value, onChange }: BarcodesFiltersProps) {
  return (
    <div className="grid gap-3 md:grid-cols-2">
      <Input
        placeholder="Search by employee or barcode value"
        value={value.query}
        onChange={(event) => onChange({ ...value, query: event.target.value })}
      />
      <select
        className="h-10 rounded-xl border border-border bg-input px-3 text-sm"
        value={value.status}
        onChange={(event) => onChange({ ...value, status: event.target.value as BarcodeListFilters["status"] })}
      >
        <option value="all">All Status</option>
        <option value="active">Active</option>
        <option value="inactive">Inactive</option>
      </select>
    </div>
  );
}
