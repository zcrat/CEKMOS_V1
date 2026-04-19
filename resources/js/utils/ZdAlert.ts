export type AlertOptions = {
  title?: string;
  text?: string;
  confirmButtonText?: string;
  cancelButtonText?: string;
};

type AlertResult = boolean;

let globalFire:
  | ((opts: AlertOptions) => Promise<AlertResult>)
  | null = null;

export function ZdAlert(opts: AlertOptions): Promise<AlertResult> {
  if (!globalFire) {
    console.warn('AlertProvider no está montado');
    return Promise.resolve(false);
  }
  return globalFire(opts);
}

export function registerAlert(fn: typeof globalFire) {
  globalFire = fn;
}

export function unregisterAlert() {
  globalFire = null;
}