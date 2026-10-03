/**
 * Icônes du kit UI (16×16, couleur = currentColor)
 */

const base = {
  width: 16,
  height: 16,
  viewBox: '0 0 16 16',
  fill: 'none',
  stroke: 'currentColor',
  'aria-hidden': true,
  style: { display: 'block' },
};

export const CheckIcon = (props) => (
  <svg {...base} {...props}>
    <path d="m2.5 8.5 4 4 7-9" />
  </svg>
);

export const PlusIcon = (props) => (
  <svg {...base} strokeLinecap="square" {...props}>
    <path d="M1.5 8h13M8 14.5v-13" />
  </svg>
);

export const MinusIcon = (props) => (
  <svg {...base} strokeLinecap="square" {...props}>
    <path d="M1.5 8h13" />
  </svg>
);

export const CaretUpDownIcon = (props) => (
  <svg {...base} fill="currentColor" stroke="none" {...props}>
    <path d="M11 10H5l3 3.5zm0-4H5l3-3.5z" />
  </svg>
);

export const CloseIcon = (props) => (
  <svg {...base} strokeLinecap="square" {...props}>
    <path d="m3 3 10 10M13 3 3 13" />
  </svg>
);
