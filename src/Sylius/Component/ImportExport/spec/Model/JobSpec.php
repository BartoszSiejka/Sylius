<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\ImportExport\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class JobSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\ImportExport\Model\Job');
    }

    public function it_implements_job_interface()
    {
        $this->shouldImplement('Sylius\Component\ImportExport\Model\JobInterface');
    }

    public function it_has_status()
    {
        $this->setStatus('new');
        $this->getStatus()->shouldReturn('new');
    }

    public function it_has_start_time()
    {
        $startTime = new \DateTime('2015-01-01');
        $this->setStartTime($startTime);
        $this->getStartTime()->shouldReturn($startTime);
    }

    public function it_has_end_time()
    {
        $endTime = new \DateTime('2015-01-01');
        $this->setEndTime($endTime);
        $this->getEndTime()->shouldReturn($endTime);
    }

    public function it_has_created_at()
    {
        $createdAt = new \DateTime('2015-01-01');
        $this->setCreatedAt($createdAt);
        $this->getCreatedAt()->shouldReturn($createdAt);
    }

    public function it_has_updated_at()
    {
        $updatedAt = new \DateTime('2015-01-01');
        $this->setUpdatedAt($updatedAt);
        $this->getUpdatedAt()->shouldReturn($updatedAt);
    }
}