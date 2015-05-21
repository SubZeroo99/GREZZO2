<?php

//      Average Days from “Repairs in Progress” to “Repairs Completed” Credits to LegitButton
        $qb = $em->getRepository('AccMainBundle:Job')
                ->createQueryBuilder('j')
                ->select('AVG(ABS(TIMESTAMPDIFF(DAY, jsi1.createdAt, jsi2.createdAt)))')
                ->join('j.statuses', 'jsi1')
                ->join('jsi1.status', 'js1')
                ->join('js1.category', 'jsc1', 'WITH', 'jsc1.code = :firstStatusCategoryCode')
                ->join('j.statuses', 'jsi2')
                ->join('jsi2.status', 'js2')
                ->join('js2.category', 'jsc2', 'WITH', 'jsc2.code = :secondStatusCategoryCode')
                ->andWhere('(SELECT COUNT(jsi1_t.id) FROM AccMainBundle:JobStatusItem jsi1_t JOIN AccMainBundle:JobStatus js1_t WITH js1_t = jsi1_t.status JOIN AccMainBundle:JobStatusCategory jsc1_t WITH jsc1_t = js1_t.category WHERE jsi1_t.job = j AND jsc1_t.code = jsc1.code AND jsi1_t.createdAt < jsi1.createdAt) = 0')
                ->andWhere('(SELECT COUNT(jsi2_t.id) FROM AccMainBundle:JobStatusItem jsi2_t JOIN AccMainBundle:JobStatus js2_t WITH js2_t = jsi2_t.status JOIN AccMainBundle:JobStatusCategory jsc2_t WITH jsc2_t = js2_t.category WHERE jsi2_t.job = j AND jsc2_t.code = jsc2.code AND jsi2_t.createdAt > jsi2.createdAt) = 0')
                ->andWhere('j.createdAt BETWEEN :from AND :to')
                ->setParameters($params)
                ->setParameter('firstStatusCategoryCode', JobStatusCategory::REPAIRS_IN_PROGRESS)
                ->setParameter('secondStatusCategoryCode', JobStatusCategory::REPAIRS_COMPLETED)
                ;
                $this->filterJobStatus($qb);
                $this->filter($qb, $bodyshop, $insuranceCompany);
                $report['Cycle Time']['Average Days from “Repairs in Progress” to “Repairs Completed”']['Combined'] =
                        $qb->getQuery()->getSingleScalarResult();
                $qb->andWhere('j.carDrivable = :carDrivable');
                $report['Cycle Time']['Average Days from “Repairs in Progress” to “Repairs Completed”']['Drivable'] =
                        $qb->setParameter('carDrivable', true)->getQuery()->getSingleScalarResult();
                $report['Cycle Time']['Average Days from “Repairs in Progress” to “Repairs Completed”']['NonDrivable'] =
                        $qb->setParameter('carDrivable', false)->getQuery()->getSingleScalarResult();
