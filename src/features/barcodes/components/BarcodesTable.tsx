import { Link } from "react-router-dom";
import { Button } from "../../../components/ui/button";
import type { BarcodeRecord } from "../types/barcode.types";

type BarcodesTableProps = {
  rows: BarcodeRecord[];
  onRegenerate: (id: string) => void;
  onDeactivate: (id: string) => void;
  onDelete: (id: string) => void;
};

export function BarcodesTable({ rows, onRegenerate, onDeactivate, onDelete }: BarcodesTableProps) {
  return (
    <div className="overflow-x-auto rounded-2xl border border-border bg-card">
      <table className="w-full border-collapse text-sm">
        <thead className="bg-muted/40 text-left text-xs uppercase tracking-wide text-muted-foreground">
          <tr>
            <th className="px-4 py-3">Employee</th>
            <th className="px-4 py-3">Barcode Value</th>
            <th className="px-4 py-3">Status</th>
            <th className="px-4 py-3">Created Date</th>
            <th className="px-4 py-3">Actions</th>
          </tr>
        </thead>
        <tbody>
          {rows.map((row) => (
            <tr key={row.id} className="border-t border-border">
              <td className="px-4 py-3">{row.employee_name ?? "-"}</td>
              <td className="px-4 py-3">{row.code}</td>
              <td className="px-4 py-3">{row.is_active ? "Active" : "Inactive"}</td>
              <td className="px-4 py-3">{row.generated_at?.slice(0, 10) ?? "-"}</td>
              <td className="px-4 py-3">
                <div className="flex flex-wrap gap-2 text-xs">
                  <Link to={`/barcodes/${row.id}`} className="rounded-md border border-border px-2 py-1">
                    View Detail
                  </Link>
                  <Button size="sm" variant="outline" onClick={() => onRegenerate(row.id)}>
                    Regenerate
                  </Button>
                  <Button size="sm" variant="outline" onClick={() => onDeactivate(row.id)}>
                    Deactivate
                  </Button>
                  <Button size="sm" variant="outline" onClick={() => onDelete(row.id)}>
                    Delete
                  </Button>
                </div>
              </td>
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
}
