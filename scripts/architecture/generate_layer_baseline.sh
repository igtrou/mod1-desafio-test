#!/usr/bin/env bash

set -euo pipefail

usage() {
    cat <<'EOF'
Usage:
  scripts/architecture/generate_layer_baseline.sh [--output-dir PATH]

Options:
  --output-dir PATH   Directory for generated files (default: docs/diagrams)
  --help              Show this help

Generated files:
  deptrac-layers.mmd  Mermaid flowchart from Deptrac layer dependencies
  deptrac-layers.dot  Graphviz DOT graph from Deptrac layer dependencies
EOF
}

OUTPUT_DIR="docs/diagrams"

while [[ $# -gt 0 ]]; do
    case "$1" in
        --output-dir)
            OUTPUT_DIR="${2:-}"
            shift 2
            ;;
        --help|-h)
            usage
            exit 0
            ;;
        *)
            echo "Unknown argument: $1" >&2
            usage
            exit 1
            ;;
    esac
done

if [[ ! -f "deptrac.yaml" ]]; then
    echo "deptrac.yaml not found in repository root." >&2
    exit 1
fi

if [[ ! -x "vendor/bin/deptrac" ]]; then
    echo "vendor/bin/deptrac not found. Run: composer install" >&2
    exit 1
fi

mkdir -p "$OUTPUT_DIR"

MERMAID_OUTPUT="${OUTPUT_DIR}/deptrac-layers.mmd"
DOT_OUTPUT="${OUTPUT_DIR}/deptrac-layers.dot"
BASELINE_DOC="docs/ARCHITECTURE_LAYER_BASELINE.md"

echo "[deptrac-diagram] Generating Mermaid baseline: ${MERMAID_OUTPUT}"
vendor/bin/deptrac analyse \
    --config-file=deptrac.yaml \
    --formatter=mermaidjs \
    --no-progress \
    --report-uncovered \
    --fail-on-uncovered \
    --output="${MERMAID_OUTPUT}"

echo "[deptrac-diagram] Generating Graphviz DOT baseline: ${DOT_OUTPUT}"
php -d error_reporting=24575 vendor/bin/deptrac analyse \
    --config-file=deptrac.yaml \
    --formatter=graphviz-dot \
    --no-progress \
    --report-uncovered \
    --fail-on-uncovered \
    --output="${DOT_OUTPUT}"

echo "[deptrac-diagram] Updating Markdown baseline: ${BASELINE_DOC}"
{
    cat <<'EOF'
# Baseline Visual de Dependencias de Camada

Este documento registra o baseline visual atual das dependencias entre camadas, gerado a partir de `deptrac.yaml`.
Use durante revisao de PR para identificar mudancas estruturais com impacto arquitetural.

## Quando usar

1. Revisar PR com alteracao de fluxo entre `Http`, `Actions`, `Services`, `Domain`, `Ports` e `Infrastructure`.
2. Comparar estado arquitetural atual contra baseline anterior.
3. Validar se alteracoes exigem ajuste de `deptrac.yaml` e/ou regras em `tests/Unit/Architecture`.

## Diagrama Mermaid (baseline atual)

```mermaid
EOF

    cat "${MERMAID_OUTPUT}"

    cat <<'EOF'
```

## Artefatos gerados

1. `docs/diagrams/deptrac-layers.mmd`
2. `docs/diagrams/deptrac-layers.dot`

## Como regenerar

```bash
composer run architecture:diagram
```

## Relacao com outros documentos

1. Regras arquiteturais e guardrails: [`ARCHITECTURE_GUIDELINES.md`](ARCHITECTURE_GUIDELINES.md)
2. Arquitetura executavel e fluxos: [`ARCHITECTURE.md`](ARCHITECTURE.md)
3. Hub de docs: [`README.md`](README.md)
EOF
} > "${BASELINE_DOC}"

echo "[deptrac-diagram] Done."
