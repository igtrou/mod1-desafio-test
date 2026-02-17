# Baseline Visual de Dependencias de Camada

Este documento registra o baseline visual atual das dependencias entre camadas, gerado a partir de `deptrac.yaml`.
Use durante revisao de PR para identificar mudancas estruturais com impacto arquitetural.

## Quando usar

1. Revisar PR com alteracao de fluxo entre `Http`, `Actions`, `Services`, `Domain`, `Ports` e `Infrastructure`.
2. Comparar estado arquitetural atual contra baseline anterior.
3. Validar se alteracoes exigem ajuste de `deptrac.yaml` e/ou regras em `tests/Unit/Architecture`.

## Diagrama Mermaid (baseline atual)

```mermaid
flowchart TD;
    Actions -->|40| Services;
    Actions -->|28| ApplicationPortsIn;
    Actions -->|53| Data;
    ApplicationPortsIn -->|15| Data;
    ApplicationPortsOut -->|14| Domain;
    Console -->|5| ApplicationPortsIn;
    Console -->|12| Framework;
    Data -->|6| DateTime;
    Data -->|13| Domain;
    Http -->|111| Framework;
    Http -->|23| ApplicationPortsIn;
    Http -->|1| Data;
    Infrastructure -->|49| Framework;
    Infrastructure -->|143| Domain;
    Infrastructure -->|29| Models;
    Infrastructure -->|29| ApplicationPortsOut;
    Infrastructure -->|16| DateTime;
    Models -->|6| Domain;
    Models -->|11| Framework;
    OpenApi -->|347| OpenApiAttributes;
    Providers -->|25| Infrastructure;
    Providers -->|5| Domain;
    Providers -->|23| ApplicationPortsOut;
    Providers -->|4| Framework;
    Services -->|35| ApplicationPortsOut;
    Services -->|38| Domain;
    Services -->|7| DateTime;
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
