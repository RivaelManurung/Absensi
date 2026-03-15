import { type PropsWithChildren } from "react";
import { cn } from "../../lib/utils";

type ModalProps = PropsWithChildren<{
  open: boolean;
  onClose: () => void;
  title: string;
}>;

export function Modal({ open, onClose, title, children }: ModalProps) {
  if (!open) {
    return null;
  }

  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4">
      <div className={cn("w-full max-w-lg rounded-2xl border border-border bg-card p-6 shadow-xl")}>
        <div className="mb-4 flex items-center justify-between">
          <h3 className="text-lg font-semibold">{title}</h3>
          <button className="rounded-md px-2 py-1 text-muted-foreground hover:bg-muted" onClick={onClose}>
            x
          </button>
        </div>
        {children}
      </div>
    </div>
  );
}
